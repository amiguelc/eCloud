<?php
namespace eCloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Ficheros;
use eCloud\UsuarioBundle\Entity\Eventos;
use eCloud\UsuarioBundle\Entity\Enlaces;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Validator\Constraints\NotBlank;

class FicherosController extends Controller{

	public function ficherosAction($ruta){
	
		//falta ajaxificar lista ficheros, y calcular checksum lado cliente.
	
		if ($this->get('security.context')->isGranted('ROLE_USER')){
		
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
					
			//Procesa ruta/carpeta. (coger ficheros de ese usuario y pasarlos a la plantilla twig, primero saber que ruta pide).
			$entity_ficheros = new Ficheros();
			
			//quitar la / de la ruta
			if(!isset($ruta)){$ruta="";}
			//quitar / del principio
			if($ruta[0]=="/"){$ruta=substr($ruta,1);}
			//quitar / del final
			if(($ruta[(strlen($ruta))-1])=="/"){$ruta=substr($ruta,0,-1);}
			//FALTA quitar las dobles // y dejar solo una por ejemplo.
				
			//Formulario de subir fichero/crear carpeta
			$formulario = $this->createFormBuilder($entity_ficheros)->add('file','file',array('label'=>'Archivo '))->add('ruta','hidden',array('data' => "/".$ruta))->getForm();
			$formulario_carpeta  = $this->createFormBuilder($entity_ficheros)->add('nombrefichero','text',array('label'=>'Carpeta'))->add('ruta','hidden',array('data' => "/".$ruta))->getForm();
			
			$em=$this->getDoctrine()->getManager();
			$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser'=>$userid));
			//Comprobacion de existencia de tal carpeta, falta devolver error, de momento redirecciona a ficheros.
			if ($ruta!=""){
				$comp_carpeta=explode("/",$ruta);
				if (is_array($comp_carpeta)){
					$total=count($comp_carpeta);
					$nombref=$comp_carpeta[$total-1];
					if ($total>=2){						
						$ruta_carpeta="";
						for ($i=0;$i<$total-1;$i++){
							$ruta_carpeta.="/".$comp_carpeta[$i];
						}
						if($ruta_carpeta[0]=="/"){$ruta_carpeta=substr($ruta_carpeta,1);}
					}else{
						$ruta_carpeta="";
					}
					
					$comprobacion=$em->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid, 'ruta' => "/".$ruta_carpeta, 'nombreFichero' => $nombref));
					if ($comprobacion==null) {
							//return $this->redirect($this->generateUrl('ficheros'), 303);
							return new Response("No existe esa carpeta");
						}
				}
			}
			//Aqui saca los ficheros de esa carpeta
			$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid, 'ruta' => "/".$ruta));
			
			//For each $ficheros cuando sea carpeta hacer una SQL para sacar el tama�o de subficheros. Esto consumir� mucho SQL. Ademas no se deberia poner aqui, sino en ficheros JSON y guardarse el valor para la app.
			$filesize_total=0;

			foreach ($ficheros as $clave => $valor){
				if ($ficheros[$clave]->getTipo()=="carpeta"){
				
					if($ficheros[$clave]->getRuta()=="/"){$carpeta_en_cuestion="/".$ficheros[$clave]->getNombreFichero();}
					else{$carpeta_en_cuestion=$ficheros[$clave]->getRuta()."/".$ficheros[$clave]->getNombreFichero();}
					
				$query = $em->createQuery('SELECT f FROM UsuarioBundle:Ficheros f WHERE f.propietario=?1 AND f.ruta LIKE ?2 OR f.propietario=?1 AND f.ruta LIKE ?3');
				$query->setParameter(1, $userid);
				$query->setParameter(2, $carpeta_en_cuestion);
				$query->setParameter(3, $carpeta_en_cuestion.'/%');
				$todos_ficheros=$query->getResult();
					
					foreach ($todos_ficheros as $clave2 => $valor2){
					//segunda comprobacion innecesaria..
						if (preg_match(":^".$carpeta_en_cuestion.":",$todos_ficheros[$clave2]->getRuta())==1){
							$filesize_total+=$todos_ficheros[$clave2]->getFilesize();
						}
					}
						
					$ficheros[$clave]->setFilesize($filesize_total);
					$filesize_total=0;
				}
			}
			
			//PASAR BYTES A MB
			foreach ($ficheros as $clave => $valor){
				$ficheros[$clave]->setFilesize(round(($ficheros[$clave]->getFilesize()/1024/1024),2));
			}
			
			//Arreglar Fecha de modificacion
			foreach ($ficheros as $clave => $valor){
				$ficheros[$clave]->setFechasCorrectas($usuarios->getZone());
			}
		
		return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig',array('ficheros' => $ficheros,'ruta' => $ruta, 'formulario' => $formulario->createView(), 'formulario_carpeta' => $formulario_carpeta->createView()));
		}
		else{
				return $this->redirect($this->generateUrl('login'), 301);
		}		
	}
	
	public function ficherosjsonAction($ruta){
	
		if ($this->get('security.context')->isGranted('ROLE_USER')){
		
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			
			//Validar y procesar ruta para que no falle en el SQL. Se podria utilizar el id de la carpeta en vez del nombre y ruta.. Seria mas rapido, sencillo y seguro. Ademas si id no existe dar error.
			if ($ruta!="/"){$ruta="/".$ruta;}
			
			$ficheros=$this->getDoctrine()->getManager()->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid, 'ruta' => $ruta));
				
			//JSON
			$encoders = array(new XmlEncoder(), new JsonEncoder());
			$normalizers = array(new GetSetMethodNormalizer());
			$serializer = new Serializer($normalizers, $encoders);		
			$jsonContent = $serializer->serialize($ficheros, 'json');
			return new Response ($jsonContent);
			
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}
		
	public function ficheroAction($id){
	
		if ($this->get('security.context')->isGranted('ROLE_USER')){
		
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em=$this->getDoctrine()->getManager();
			
			//Validar idfichero.			
			$fichero=$em()->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid, 'idFichero' => $id));
			if ($fichero==NULL){return new Response ("Fichero no encontrado");}
			
			$usuario=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser' => $userid));
			
			$fichero[0]->setFechasCorrectas($usuario->getZone());
				
			return $this->render('UsuarioBundle:Cuenta:fichero.html.twig',array('fichero'=>$fichero));
		}
		else{
				return $this->redirect($this->generateUrl('login'), 301);
		}	
	}
	
	
	public function ficherojsonAction($id){
		
		if ($this->get('security.context')->isGranted('ROLE_USER')){
		
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			
			//Falta validar datos del ID.
				
			$ficheros=$this->getDoctrine()->getManager()->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid, 'idFichero' => $id));
			if ($ficheros==NULL){return new Response ("Fichero no encontrado");}

			//JSON
			$encoders = array(new XmlEncoder(), new JsonEncoder());
			$normalizers = array(new GetSetMethodNormalizer());
			$serializer = new Serializer($normalizers, $encoders);		
			$jsonContent = $serializer->serialize($ficheros, 'json');
			return new Response ($jsonContent);		
		}
		else{
				return $this->redirect($this->generateUrl('login'), 301);
		}	
	}
	
	public function subirAction(Request $request){
	
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			$ficheros = new Ficheros();
			//$data = $this->getRequest()->request->all();
			//$name = $data['form']['nombrefichero'];
			
			$carpeta=FALSE;
			if(isset($_POST['form']['nombrefichero'])){
				$carpeta=TRUE;
				$formulario  = $this->createFormBuilder($ficheros)->add('nombrefichero','text')->add('ruta','hidden', array('constraints' => new NotBlank()))->getForm();
			}
			else{
				$formulario = $this->createFormBuilder($ficheros)->add('file','file')->add('ruta','hidden', array('constraints' => new NotBlank()))->getForm();
			}
			
			if ($request->isMethod('POST')) {
			
				$formulario->handleRequest($request);
				if ($formulario->isValid()) {
					$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
					
					$em = $this->getDoctrine()->getManager();
						
					//Comprobaciones de nombre de fichero, carpeta o ruta. Si el fichero/carpeta ya existe. Caracteres prohibidos ( / \ : ? < > ' " ~ * | )
					
					if($carpeta==TRUE){
					//Comprobaciones a carpeta.
						if(preg_match("/(\/|\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['nombrefichero'])==1){ return  $response = new Response("Error en el nombre de la carpeta");}
						if(preg_match("/(\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['ruta'])==1){ return  $response = new Response("Error en la ruta de la carpeta");}
						if(preg_match("/\.\.\//",$_POST['form']['ruta'])==1){ return  $response = new Response("No se permite rutas con ../");}
						if($_POST['form']['ruta'][0]!="/"){$_POST['form']['ruta']="/".$_POST['form']['ruta'];}
						
						$query=$em->createQuery("SELECT f.tipo FROM UsuarioBundle:Ficheros f WHERE f.propietario=?1 and f.ruta like ?2 and f.nombreFichero like ?3");
						$query->setParameter(1, $userid);
						$query->setParameter(2, $_POST['form']['ruta']);
						$query->setParameter(3, $_POST['form']['nombrefichero']);
						$mismacarpeta = $query->getOneOrNullResult();
						if($mismacarpeta!=null){
							if($mismacarpeta['tipo']=="carpeta"){
								return  $response = new Response("Esa carpeta ya existe en el directorio actual");
							}
							else{
								return  $response = new Response("En esa carpeta existe un fichero con el mismo nombre");
							}
						}
						//$namefile=$_POST['form']['nombrefichero'];
					}
					else{
					//Comprobaciones a fichero. Falta a�adir if is sets.	
						if(preg_match("/(\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['ruta'])==1){ return  $response = new Response("Error en la ruta del fichero");}
						if(preg_match("/\.\.\//",$_POST['form']['ruta'])==1){ return  $response = new Response("No se permite rutas con ../");}
						if(preg_match("/(\/|\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_FILES['form']['name']['file'])==1){ return  $response = new Response("Error en el nombre del fichero");}
						if($_POST['form']['ruta'][0]!="/"){$_POST['form']['ruta']="/".$_POST['form']['ruta'];}
						
						$query=$em->createQuery("SELECT f.tipo FROM UsuarioBundle:Ficheros f WHERE f.propietario=?1 and f.ruta like ?2 and f.nombreFichero like ?3");
						$query->setParameter(1, $userid);
						$query->setParameter(2, $_POST['form']['ruta']);
						$query->setParameter(3, $_FILES['form']['name']['file']);
						$mismofichero = $query->getOneOrNullResult();
						//if($mismofichero!=null){return  $response = new Response("Ese fichero ya existe en el directorio actual");}
						if($mismofichero!=null){
							if($mismofichero['tipo']=="carpeta"){
								return  $response = new Response("En esa carpeta existe una carpeta con el mismo nombre");
							}
							else{
								return  $response = new Response("En esa carpeta existe un fichero con el mismo nombre");
							}
						}
						//$namefile=$_FILES['form']['name']['file'];
					}
					
					//Comprobacion de si existe la carpeta en la cual se va a crear/subir fichero/carpeta
					$ruta=$_POST['form']['ruta'];
					if ($ruta!="/"){
						if ($ruta!=""){
							$comp_carpeta=explode("/",$ruta);
							if (is_array($comp_carpeta)){
								$total=count($comp_carpeta);
								$nombref=$comp_carpeta[$total-1];
								if ($total>=2){
									$ruta_carpeta="";
									for ($i=0;$i<$total-1;$i++){
										$ruta_carpeta.="/".$comp_carpeta[$i];
									}
									if($ruta_carpeta[0]=="/"){$ruta_carpeta=substr($ruta_carpeta,1);}
									if($ruta_carpeta[0]=="/"){$ruta_carpeta=substr($ruta_carpeta,1);}
								}else{
									$ruta_carpeta="";
								}
								
								$comprobacion=$em->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid, 'ruta' => "/".$ruta_carpeta, 'nombreFichero' => $nombref));
								if ($comprobacion==null) {
										//return $this->redirect($this->generateUrl('ficheros'), 303);
										return new Response("Ese carpeta no existe");
									}
							}
						}
					}
					//Ficheros normales///
					//propietario, nombre_fichero, nombre_real_fisico, tipo, ruta, filesize, checksum, fecha_subida, total_descargas, permiso
					$ficheros->setPropietario($userid);
					
					//$ficheros->setRuta("/");
					//$ruta=$formulario["ruta"]->getData();
					$ficheros->setRuta($ruta);
					$ficheros->setfechaSubida(new \Datetime(null,new \DateTimeZone("UTC")));
					$ficheros->setModificacion(new \Datetime(null,new \DateTimeZone("UTC")));
					$ficheros->settotalDescargas("0");
					$ficheros->setPermiso("si");

					//Si es carpeta o fichero//
					if($carpeta==TRUE){
						$ficheros->setNombreFichero($_POST['form']['nombrefichero']);
						$ficheros->setNombreRealFisico($_POST['form']['nombrefichero']);
						$ficheros->setTipo("carpeta");
						$ficheros->setMime('carpeta');
						$ficheros->setChecksum('0');
						$ficheros->setFilesize('0');
						//crear carpeta en el caso de crear carpetas
						if($carpeta==TRUE){mkdir($this->container->getParameter('var_archivos').$userid.$ficheros->getRuta()."/".$ficheros->getnombreFichero());}
					}else {
						$ficheros->setTipo("fichero");
						$var_archivos=$this->container->getParameter('var_archivos');
						//FALTA COMPROBAR SI YA EXISTE EL MISMO FICHERO HACIENDO UNA SQL, INCLUIR LA COMPROBACION EN EL CLIENTE
						$ficheros->upload($var_archivos);
					}
					
					
					//Sumar espacio ocupado a la cuenta del userid, pero antes hay que comprobar que no supera el espacio.
					$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser'=>$userid));
					$ocupado=$usuarios->getOcupado();
					$tamanyo=$ficheros->getFilesize();
					$limite=$usuarios->getLimite();
					//return  $response = new Response($ocupado."/".$tamanyo."/".$limite);
					if($ocupado+$tamanyo<=$limite){
						$usuarios->setOcupado($usuarios->getOcupado()+$ficheros->getFilesize());
					}
					else {
						//Borra fichero previamente subido a la carpeta <- Hay que mejorarlo para que corte la ejecucion del script dentro de fichero.php antes de copiar nada, asi es mas rapido. 
						$ficheros->remove($this->container->getParameter('var_archivos'));
						return  $response = new Response("Sin espacio");
					}
					
					//ejecutar sentencias
					$em->persist($ficheros);
					$em->persist($usuarios);
					$em->flush();
					
					
					//Eventos//
					$eventos=new Eventos();
					//id_evento,id_user,accion,id_fichero,nombre_fichero_antiguo,nombre_fichero_nuevo,fecha 
					$eventos->setIdUser($userid);
					if ($carpeta==FALSE){$eventos->setaccion("Has subido el fichero ".$ficheros->getnombreFichero());$eventos->setTipo("10");}
					else{$eventos->setaccion("Has creado la carpeta ".$ficheros->getnombreFichero()." en ".$ficheros->getRuta());$eventos->setTipo("11");}
					$eventos->setIdFichero($ficheros->getIdFichero());
					$eventos->setNombreFicheroAntiguo($ficheros->getnombreFichero());
					$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
					$eventos->setFecha(new \Datetime(null,new \DateTimeZone("UTC")));
					$eventos->setRuta($ficheros->getRuta());
					
					$em2 = $this->getDoctrine()->getManager();
					$em2->persist($eventos);
					$em2->flush();
					
					//Fichero subido correctamente
					return  $response = new Response("Subido correctamente");
				}
				else{
					//Formulario no valido.
					return  $response = new Response("Formulario no valido");
				}
			}
			else{
				//Peticion GET
				return  $response = new Response("Peticion GET");
			}			
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}

	public function modificarAction(Request $request,$fichero){
   
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			
			$document = new Ficheros();
			$formulario = $this->createFormBuilder($document)->add('nombrefichero','text')->add('ruta','text')->getForm();
			$em = $this->getDoctrine()->getManager();
			$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser'=>$userid));			
			
			if ($request->isMethod('POST')) {
				//guardar datos modificados
				$formulario->bind($request);
				if ($formulario->isValid()) {
									
					//Validar datos.
					if($_POST['form']['ruta']=="" || $_POST['form']['nombrefichero']==""){ return  $response = new Response("Faltan datos");}
					if($_POST['form']['ruta'][0]!="/"){$_POST['form']['ruta']="/".$_POST['form']['ruta'];}
					if(preg_match("/(\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['ruta'])==1){ return  $response = new Response("Error en la ruta del fichero");}
					if(preg_match("/\.\.\//",$_POST['form']['ruta'])==1){ return  $response = new Response("No se permite rutas con ../");}
					if(preg_match("/(\/|\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['nombrefichero'])==1){ return  $response = new Response("Error en el nombre del fichero");}
					
					$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
					if ($ficheros==null){ return  $response = new Response("Ese fichero no existe o no es tuyo");}
					
					if($_POST['form']['nombrefichero']==$ficheros->getNombreFichero() && $_POST['form']['ruta']==$ficheros->getRuta()){return $this->redirect($this->generateUrl('ficheros'),303);} //Ningun cambio
					elseif($_POST['form']['nombrefichero']==$ficheros->getNombreFichero() && $_POST['form']['ruta']!=$ficheros->getRuta()){$accion="mover";} //Mover fichero, comprobar que la nueva carpeta existe y que en ella no exista la misma carpeta o fichero.
					elseif($_POST['form']['nombrefichero']!=$ficheros->getNombreFichero() && $_POST['form']['ruta']!=$ficheros->getRuta()){$accion="mover y cambiar";} //Comprobar si carpeta nueva existe, si fichero existe y evento has modificado el nombre y movido a...
					elseif($_POST['form']['nombrefichero']!=$ficheros->getNombreFichero() && $_POST['form']['ruta']==$ficheros->getRuta()){$accion="cambiar";}//Has cambiado de nombre..Comprobar si fichero ya existe.
					else{ return $response=new Response("Error");}
					
					$nombre_antiguo=$ficheros->getNombreFichero();
					$nombre_nuevo=$_POST['form']['nombrefichero'];	//$nombre_nuevo=$formulario["nombrefichero"]->getData();
					$ruta_nueva=$_POST['form']['ruta'];				//$ruta_nueva=$formulario["ruta"]->getData();	
					if (strlen($ruta_nueva)>1 && $ruta_nueva[strlen($ruta_nueva)-1]=="/"){$ruta_nueva=substr($ruta_nueva, 0, -1);}//Para quitar el ultimo / de la ruta si lo tuviera. Cuidao con la raiz
					$ruta_antigua=$ficheros->getRuta();
					$ruta_absoluta_antigua=$this->container->getParameter('var_archivos').$userid.$ficheros->getRuta()."/".$nombre_antiguo;
					$ruta_absoluta_nueva=$this->container->getParameter('var_archivos').$userid.$ruta_nueva."/".$nombre_nuevo;
					
					$ficheros->setRuta($ruta_nueva);
					$ficheros->setNombreFichero($nombre_nuevo);
					$ficheros->setNombreRealFisico($nombre_nuevo);
					$ficheros->setModificacion(new \Datetime(null,new \DateTimeZone("UTC")));
										
					//Eventos//
					$eventos=new Eventos();
					$eventos->setIdUser($userid);
					
					if($accion=="cambiar"){
						if($ficheros->getTipo()=='fichero'){
							$eventos->setaccion("Has cambiado el nombre al fichero ".$nombre_antiguo." por ".$nombre_nuevo);
							$eventos->setTipo("20");
						}
						else{
							$eventos->setaccion("Has cambiado el nombre a la carpeta ".$nombre_antiguo." por ".$nombre_nuevo);
							$eventos->setTipo("21");
						}
					}elseif($accion=="mover"){
						//Comprobacion de si carpeta nueva existe.
						if($ruta_antigua=="/"){$pattern="/^".addcslashes("/".$nombre_antiguo,"/")."/";}else{$pattern="/^".addcslashes($ruta_antigua."/".$nombre_antiguo,"/")."/";}
						if(preg_match($pattern,$ruta_nueva)==1){ return  $response = new Response("No se puede mover a subcarpeta");}
						$posicion_barra=strrpos($ruta_nueva, "/");
						$nombre_carpeta_nueva=substr($ruta_nueva, $posicion_barra+1);
						$ruta_carpeta_nueva=substr($ruta_nueva, 0, $posicion_barra);
						if($nombre_carpeta_nueva==""){$nombre_carpeta_nueva="/";} //Mover a la raiz, evitar sql directamente. La raiz existe siempre.
						if($ruta_carpeta_nueva==""){$ruta_carpeta_nueva="/";}
						//return $response = new Response($nombre_carpeta_nueva."<br>".$ruta_carpeta_nueva);
						
						if($nombre_carpeta_nueva!="/"){
							$query = $em->createQuery('SELECT f.nombreFichero FROM UsuarioBundle:Ficheros f WHERE f.nombreFichero LIKE ?1 and f.ruta LIKE ?2 and f.propietario=?3 and f.tipo like ?4');
							$query->setParameter(1, $nombre_carpeta_nueva);
							$query->setParameter(2, $ruta_carpeta_nueva);
							$query->setParameter(3, $userid);
							$query->setParameter(4, "carpeta");
							$res=$query->getOneOrNullResult();
							if($res==null){return $response = new Response("No existe esa carpeta");}
						}
					
						if($ficheros->getTipo()=='fichero'){
							$eventos->setaccion("Has movido el fichero ".$nombre_nuevo." a ".$ruta_nueva);
							$eventos->setTipo("30");
						}
						else{
							$eventos->setaccion("Has movido la carpeta ".$nombre_nuevo." a ".$ruta_nueva);
							$eventos->setTipo("31");
						}
					}elseif($accion=="mover y cambiar"){
						//Comprobacion de si carpeta nueva existe. //Copiado integramente de la anterior.
						if($ruta_antigua=="/"){$pattern="/^".addcslashes("/".$nombre_antiguo,"/")."/";}else{$pattern="/^".addcslashes($ruta_antigua."/".$nombre_antiguo,"/")."/";}
						if(preg_match($pattern,$ruta_nueva)==1){ return  $response = new Response("No se puede mover a subcarpeta");}
						$posicion_barra=strrpos($ruta_nueva, "/");
						$nombre_carpeta_nueva=substr($ruta_nueva, $posicion_barra+1);
						$ruta_carpeta_nueva=substr($ruta_nueva, 0, $posicion_barra);
						if($nombre_carpeta_nueva==""){$nombre_carpeta_nueva="/";} //Mover a la raiz, evitar sql directamente. La raiz existe siempre.
						if($ruta_carpeta_nueva==""){$ruta_carpeta_nueva="/";}
						//return $response = new Response($nombre_carpeta_nueva."<br>".$ruta_carpeta_nueva);
						
						if($nombre_carpeta_nueva!="/"){
							$query = $em->createQuery('SELECT f.nombreFichero FROM UsuarioBundle:Ficheros f WHERE f.nombreFichero LIKE ?1 and f.ruta LIKE ?2 and f.propietario=?3 and f.tipo like ?4');
							$query->setParameter(1, $nombre_carpeta_nueva);
							$query->setParameter(2, $ruta_carpeta_nueva);
							$query->setParameter(3, $userid);
							$query->setParameter(4, "carpeta");
							$res=$query->getOneOrNullResult();
							if($res==null){return $response = new Response("No existe esa carpeta");}
						}
						if($ficheros->getTipo()=='fichero'){
							$eventos->setaccion("Has cambiado el nombre al fichero ".$nombre_antiguo." por ".$nombre_nuevo." y lo has movido a ".$ruta_nueva);
							$eventos->setTipo("40");
						}
						else{
							$eventos->setaccion("Has cambiado el nombre a la carpeta ".$nombre_antiguo." por ".$nombre_nuevo." y lo has movido a ".$ruta_nueva);
							$eventos->setTipo("41");
						}
					}else{return $response=new Response("Error");}
					
					//Comprobacion de si fichero ya existe en esa carpeta independientemente de su tipo.
					$query = $em->createQuery('SELECT f.nombreFichero FROM UsuarioBundle:Ficheros f WHERE f.nombreFichero LIKE ?1 and f.ruta LIKE ?2 and f.propietario=?3');
					$query->setParameter(1, $nombre_nuevo);
					$query->setParameter(2, $ruta_nueva);
					$query->setParameter(3, $userid);
					$res=$query->getOneOrNullResult();
					if($res['nombreFichero']==$nombre_nuevo){
						if($ficheros->getTipo()=="fichero"){
							return $response = new Response("Ya existe un fichero en esa carpeta con el mismo nombre");
						}
						else{
							return $response = new Response("Ya existe una carpeta en esa carpeta con el mismo nombre");
						}
					}
					
					
					$eventos->setIdFichero($ficheros->getIdFichero());
					$eventos->setNombreFicheroAntiguo($nombre_antiguo);
					$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
					$eventos->setFecha(new \Datetime(null,new \DateTimeZone("UTC")));
					$eventos->setRuta($ficheros->getRuta());
					
			
					if($ficheros->getTipo()=='carpeta'){
					
					//Cambia ruta subficheros.
					$query = $em->createQuery('UPDATE UsuarioBundle:Ficheros f SET f.ruta = ?1 WHERE f.ruta LIKE ?2 and f.propietario=?3');
					if($ruta_nueva=="/"){$query->setParameter(1, "/".$nombre_nuevo);}else{$query->setParameter(1, $ruta_nueva."/".$nombre_nuevo);}
					if ($ruta_antigua=="/"){$query->setParameter(2, "/".$nombre_antiguo);}else{$query->setParameter(2, $ruta_antigua."/".$nombre_antiguo);}
					$query->setParameter(3, $userid);
					$query->getResult();
					
					
					//Saca las rutas de los sub-subficheros a cambiar
					$query = $em->createQuery('SELECT f.ruta FROM UsuarioBundle:Ficheros f WHERE f.ruta LIKE ?1');
					if ($ruta_antigua=="/"){$query->setParameter(1, "/".$nombre_antiguo.'/%');}else{$query->setParameter(1, $ruta_antigua."/".$nombre_antiguo.'/%');}
					//Saca rutas a cambiar de sub-subficheros.
					$archivos=$query->getResult();
					
					//Cambiar la ruta del principio
					if ($ruta_antigua=="/"){$patron=$ruta_antigua.$nombre_antiguo."/";}else{$patron=$ruta_antigua."/".$nombre_antiguo.'/';}
					if ($ruta_nueva=="/"){$sustitucion="/".$nombre_nuevo;}else{$sustitucion=$ruta_nueva."/".$nombre_nuevo;}
					//Foreach que por cada subfichero cambia su ruta
					foreach ($archivos as $clave => $valor){
						$ruta_modificada_sub_subficheros=preg_replace(":^".$patron.":",$sustitucion."/",$archivos[$clave]['ruta']);
						
						$query = $em->createQuery('UPDATE UsuarioBundle:Ficheros f SET f.ruta = ?1 WHERE f.ruta LIKE ?2 and f.propietario=?3');
						$query->setParameter(1, $ruta_modificada_sub_subficheros);
						$query->setParameter(2, $archivos[$clave]['ruta']);
						$query->setParameter(3, $userid);
						$query->getResult();
						
					}
					
					
					}
				
					$em->persist($eventos);					
					$em->persist($ficheros);
					$em->flush();
					
					/*
					//Detectar SO y preparar ruta para ello.
					if (PHP_OS=="WINNT"){
					
					}else{
					//Convierte rutas windows a unix. Codigo no probado.
					$ruta_absoluta_antigua=preg_replace(":\:","/",$ruta_absoluta_antigua);
					$ruta_absoluta_nueva=preg_replace(":\:","/",$ruta_absoluta_nueva);
					}
					*/
					$ruta_absoluta_antigua=str_replace("//", "/", $ruta_absoluta_antigua);
					$ruta_absoluta_nueva=str_replace("//", "/", $ruta_absoluta_nueva);
					
					rename($ruta_absoluta_antigua,$ruta_absoluta_nueva);
										
					return $this->redirect($this->generateUrl('ficheros'),303);
				}
			}
			else{
				//mostrar datos y formulario de modificar datos, incluye el de crear link
				
				$ficheros2=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
				
				if($ficheros2==null){return new Response("El fichero no existe o t&uacute; no eres el propietario.");}
				//Arreglar Fechas
				$ficheros2->setFechasCorrectas($usuarios->getZone());
				//return new Response(ini_get('date.timezone'));
				
				//id_fichero,propietario,nombre_fichero,nombre_real_fisico,ruta,filesize,checksum,fecha_subida,total_descargas,permiso,tipo 
								
				$formulario = $this->createFormBuilder($document)->add('nombrefichero','text', array('data'=> $ficheros2->getNombreFichero()))->add('ruta','text',array ('data'=> $ficheros2->getRuta()))->getForm();
				
				//LINK
				//idEnlace,url,idFichero,propietario,fechaInicio,fechaExpiracion,usuarios(anonimos,registrados)
				$entity_enlace=new Enlaces();
				$formulario_link = $this->createFormBuilder($entity_enlace)->add('url','text')->add('idFichero','hidden',array ('data'=> $fichero))->add('propietario','hidden',array ('data'=> $userid))->add('fechaInicio','date')->add('fechaExpiracion','date')->add('usuarios','choice', array('choices' => array('todos' => 'Todos', 'registrados' => 'Solo registrados')))->getForm();
				
				return $this->render('UsuarioBundle:Cuenta:modificar.html.twig',array('fichero'=>$fichero,'ficheros' => $ficheros2,'formulario'=>$formulario->createView(),'formulario_link'=>$formulario_link->createView()));
			}			
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}
	
	public function modificarjsonAction(Request $request,$fichero){
	//Modificar o mover fichero segun si se recibe nuevo nombre o nueva ruta. Codigo copiado de modificarAction. SIN TERMINAR.
   
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();		
			
			$em = $this->getDoctrine()->getManager();
			
			if ($request->isMethod('POST')) {
				$codigo="00";
			
				$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
				if ($ficheros==null){ return  $response = new Response("E_6X4.1");} //Ese fichero no existe o no es tuyo
				$fichero_movido=$ficheros->getNombreFichero();
				
				$ficheros->setModificacion(new \Datetime(null,new \DateTimeZone("UTC")));
				
				//Validar datos.
				if(isset($_POST['ruta'])){
					//Mover fichero, comprobar que la nueva carpeta existe y que en ella no exista la misma carpeta o fichero.
					$accion="mover";					
					if(preg_match("/(\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['ruta'])==1){ return  $response = new Response("E_6X4.2");} //Error en la ruta del fichero
					if(preg_match("/\.\.\//",$_POST['ruta'])==1){ return  $response = new Response("E_6X4.3");}	//No se permite rutas con ../
					if($_POST['ruta'][0]!="/"){$_POST['ruta']="/".$_POST['ruta'];}
					
					$ruta_nueva=$_POST['ruta'];
					
					if (strlen($ruta_nueva)>1 && $ruta_nueva[strlen($ruta_nueva)-1]=="/"){$ruta_nueva=substr($ruta_nueva, 0, -1);}//Para quitar el ultimo / de la ruta si lo tuviera. Cuidao con la raiz
					$ruta_antigua=$ficheros->getRuta();
					$ruta_absoluta_antigua=$this->container->getParameter('var_archivos').$userid.$ficheros->getRuta()."/".$fichero_movido;
					$ruta_absoluta_nueva=$this->container->getParameter('var_archivos').$userid.$ruta_nueva."/".$fichero_movido;
					
					$ficheros->setRuta($ruta_nueva);
																	
				
					//Comprobacion de si carpeta nueva existe.
					if($ruta_antigua=="/"){$pattern="/^".addcslashes("/".$fichero_movido,"/")."/";}else{$pattern="/^".addcslashes($ruta_antigua."/".$fichero_movido,"/")."/";}
					if(preg_match($pattern,$ruta_nueva)==1){ return  $response = new Response("E_6X4.4");} //No se puede mover a subcarpeta
					$posicion_barra=strrpos($ruta_nueva, "/");
					$nombre_carpeta_nueva=substr($ruta_nueva, $posicion_barra+1);
					$ruta_carpeta_nueva=substr($ruta_nueva, 0, $posicion_barra);
					if($nombre_carpeta_nueva==""){$nombre_carpeta_nueva="/";} //Mover a la raiz, evitar sql directamente. La raiz existe siempre.
					if($ruta_carpeta_nueva==""){$ruta_carpeta_nueva="/";}
					//return $response = new Response($nombre_carpeta_nueva."<br>".$ruta_carpeta_nueva);
					
					if($nombre_carpeta_nueva!="/"){
						$query = $em->createQuery('SELECT f.nombreFichero FROM UsuarioBundle:Ficheros f WHERE f.nombreFichero LIKE ?1 and f.ruta LIKE ?2 and f.propietario=?3 and f.tipo like ?4');
						$query->setParameter(1, $nombre_carpeta_nueva);
						$query->setParameter(2, $ruta_carpeta_nueva);
						$query->setParameter(3, $userid);
						$query->setParameter(4, "carpeta");
						$res=$query->getOneOrNullResult();
						if($res==null){return $response = new Response("E_6X4.5");} //No existe esa carpeta
					}
					
					//Eventos//
					$eventos=new Eventos();
					$eventos->setIdUser($userid);
				
					if($ficheros->getTipo()=='fichero'){
						$codigo="M_301";
						$eventos->setaccion("Has movido el fichero ".$fichero_movido." a ".$ruta_nueva);
						$eventos->setTipo("30");
					}
					else{
						$codigo="M_311";
						$eventos->setaccion("Has movido la carpeta ".$fichero_movido." a ".$ruta_nueva);
						$eventos->setTipo("31");
					}
					
					//Comprobacion de si fichero ya existe en esa carpeta independientemente de su tipo.
					$query = $em->createQuery('SELECT f.nombreFichero FROM UsuarioBundle:Ficheros f WHERE f.nombreFichero LIKE ?1 and f.ruta LIKE ?2 and f.propietario=?3');
					$query->setParameter(1, $fichero_movido);
					$query->setParameter(2, $ruta_nueva);
					$query->setParameter(3, $userid);
					$res=$query->getOneOrNullResult();
					if($res['nombreFichero']==$fichero_movido){
						if($ficheros->getTipo()=="fichero"){
							return $response = new Response("E_6X4.6"); //Ya existe un fichero en esa carpeta con el mismo nombre
						}
						else{
							return $response = new Response("E_6X4.7"); //Ya existe una carpeta en esa carpeta con el mismo nombre
						}
					}
								
					
				}elseif (isset($_POST['nombrefichero'])){
					$accion="cambiar";//Has cambiado de nombre..Comprobar si fichero ya existe.
					if(preg_match("/(\/|\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['nombrefichero'])==1){ return  $response = new Response("E_6X4.8");} //Error en el nombre del fichero
					
					$nombre_antiguo=$ficheros->getNombreFichero();
					$nombre_nuevo=$_POST['nombrefichero'];
					
					$ficheros->setNombreFichero($nombre_nuevo);
					$ficheros->setNombreRealFisico($nombre_nuevo);

					//Utilizado por el rename
					$ruta_absoluta_antigua=$this->container->getParameter('var_archivos').$userid.$ficheros->getRuta()."/".$fichero_movido;
					$ruta_absoluta_nueva=$this->container->getParameter('var_archivos').$userid.$ficheros->getRuta()."/".$nombre_nuevo;
					
					//Eventos//
					$eventos=new Eventos();
					$eventos->setIdUser($userid);
					
					if($ficheros->getTipo()=='fichero'){
						$codigo="M_201";
						$eventos->setaccion("Has cambiado el nombre al fichero ".$nombre_antiguo." por ".$nombre_nuevo);
						$eventos->setTipo("20");
					}
					else{
						$codigo="M_211";
						$eventos->setaccion("Has cambiado el nombre a la carpeta ".$nombre_antiguo." por ".$nombre_nuevo);
						$eventos->setTipo("21");
					}
					
					//Comprobacion de si fichero ya existe en esa carpeta independientemente de su tipo.
					$query = $em->createQuery('SELECT f.nombreFichero FROM UsuarioBundle:Ficheros f WHERE f.nombreFichero LIKE ?1 and f.ruta LIKE ?2 and f.propietario=?3');
					$query->setParameter(1, $nombre_nuevo);
					$query->setParameter(2, $ficheros->getRuta());
					$query->setParameter(3, $userid);
					$res=$query->getOneOrNullResult();
					if($res['nombreFichero']==$nombre_nuevo){
						if($ficheros->getTipo()=="fichero"){
							return $response = new Response("E_6X4.6"); //Ya existe un fichero en esa carpeta con el mismo nombre
						}
						else{
							return $response = new Response("E_6X4.7"); //Ya existe una carpeta en esa carpeta con el mismo nombre
						}
					}				
					
				}else{
					return new Response("D_XX0"); //Faltan datos
				}
			
			
					$eventos->setIdFichero($ficheros->getIdFichero());
					$eventos->setNombreFicheroAntiguo($fichero_movido);
					$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
					$eventos->setFecha(new \Datetime(null,new \DateTimeZone("UTC")));
					//return new Response(var_dump(new \Datetime()));
					$eventos->setRuta($ficheros->getRuta());
					
					
					//REVISAR ESTO...PORQUE PUEDE SER CAMBIO NOMBRE O MOVER FICHERO
					if($ficheros->getTipo()=='carpeta'){
						
						if(isset($_POST['nombrefichero'])){$ruta_nueva=$ficheros->getRuta();$ruta_antigua=$ficheros->getRuta();}
						//Cambia ruta subficheros.
						$query = $em->createQuery('UPDATE UsuarioBundle:Ficheros f SET f.ruta = ?1 WHERE f.ruta LIKE ?2 and f.propietario=?3');
						if($ruta_nueva=="/"){$query->setParameter(1, "/".$ficheros->getnombreFichero());}else{$query->setParameter(1, $ruta_nueva."/".$ficheros->getnombreFichero());}
						if ($ruta_antigua=="/"){$query->setParameter(2, $ruta_antigua.$fichero_movido);}else{$query->setParameter(2, $ruta_antigua."/".$fichero_movido);}
						$query->setParameter(3, $userid);
						$query->getResult();						
						
						//Saca las rutas de los sub-subficheros a cambiar
						$query = $em->createQuery('SELECT f.ruta FROM UsuarioBundle:Ficheros f WHERE f.ruta LIKE ?1');
						if ($ruta_antigua=="/"){$query->setParameter(1, $ruta_antigua.$fichero_movido.'/%');}else{$query->setParameter(1, $ruta_antigua."/".$fichero_movido.'/%');}
						//Saca rutas a cambiar de sub-subficheros.
						$archivos=$query->getResult();
						
						//Cambiar la ruta del principio
						if ($ruta_antigua=="/"){$patron=$ruta_antigua.$fichero_movido."/";}else{$patron=$ruta_antigua."/".$fichero_movido.'/';}
						if ($ruta_nueva=="/"){$sustitucion="/".$ficheros->getnombreFichero();}else{$sustitucion=$ruta_nueva."/".$ficheros->getnombreFichero();}
						//Foreach que por cada subfichero cambia su ruta
						foreach ($archivos as $clave => $valor){
							$ruta_modificada_sub_subficheros=preg_replace(":^".$patron.":",$sustitucion."/",$archivos[$clave]['ruta']);
							
							$query = $em->createQuery('UPDATE UsuarioBundle:Ficheros f SET f.ruta = ?1 WHERE f.ruta LIKE ?2 and f.propietario=?3');
							$query->setParameter(1, $ruta_modificada_sub_subficheros);
							$query->setParameter(2, $archivos[$clave]['ruta']);
							$query->setParameter(3, $userid);
							$query->getResult();
							
						}				
					}
					
					
				
					$em->persist($eventos);
					$em->persist($ficheros);
					$em->flush();
					
					/*
					//Detectar SO y preparar ruta para ello.
					if (PHP_OS=="WINNT"){
					
					}else{
					//Convierte rutas windows a unix. Codigo no probado.
					$ruta_absoluta_antigua=preg_replace(":\:","/",$ruta_absoluta_antigua);
					$ruta_absoluta_nueva=preg_replace(":\:","/",$ruta_absoluta_nueva);
					}
					*/
					$ruta_absoluta_antigua=str_replace("//", "/", $ruta_absoluta_antigua);
					$ruta_absoluta_nueva=str_replace("//", "/", $ruta_absoluta_nueva);
					
					rename($ruta_absoluta_antigua,$ruta_absoluta_nueva);
					//return new Response(var_dump($ruta_absoluta_antigua)." - ".var_dump($ruta_absoluta_nueva));
				
					return new Response($codigo);
			}
		}
	}
	
	public function borrarAction(Request $request,$fichero){
   
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			if($request->isMethod("POST")){
				$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
				if ($request->request->get('_token')!==$request->getSession()->get("_csrf/form",null)){return new Response("Invalidad Token");};

				$em=$this->getDoctrine()->getManager();
				//Falta antes de hacer la SQL validar la variable fichero, aunque en teoria se hace en el router.
				
				$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
				if ($ficheros==null){ return  $response = new Response("Ese fichero no es tuyo o no existe");}
				$ruta_local=$this->container->getParameter('var_archivos').$userid.print_r($ficheros->getRuta(), true)."/".print_r($ficheros->getNombreFichero(), true);
				//$ruta_local=str_replace("/", "\\", $ruta_local);
				$ruta_local=str_replace("//", "/", $ruta_local);
				
				$codigo="M_5X1";
				//Si es fichero
				if ($ficheros->getTipo()=="fichero"){
					$codigo="M_501";
					//Eventos//
					$eventos=new Eventos();
					$eventos->setIdUser($userid);
					$eventos->setaccion("Has borrado el fichero ".$ficheros->getnombreFichero());
					$eventos->setTipo("50");
					$eventos->setIdFichero($fichero);
					$eventos->setNombreFicheroAntiguo($ficheros->getnombreFichero());
					$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
					$eventos->setFecha(new \Datetime(null,new \DateTimeZone("UTC")));
					$eventos->setRuta($ficheros->getRuta());
					
					//Restar espacio ocupado a la cuenta del userid.
					$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser'=>$userid));
					$usuarios->setOcupado($usuarios->getOcupado()-$ficheros->getFilesize());
					
					//ejecutar sentencias
					$em->persist($eventos);
					$em->persist($usuarios);
					$em->remove($ficheros);			
					$em->flush();
					//borrar fichero
					if (file_exists($ruta_local)){
						unlink($ruta_local);
					}
				}else{
					//Si es carpeta
					$codigo="M_511";
					$filesize_total_restar=0;
					$ruta_carpeta=str_replace('//','/',$ficheros->getRuta()."/".$ficheros->getNombreFichero());
					$query=$em->createQuery('SELECT f FROM UsuarioBundle:Ficheros f WHERE f.propietario=?1 and f.ruta like ?2 ORDER BY f.tipo DESC');
					$query->setParameter(1, $userid);
					$query->setParameter(2, $ruta_carpeta."%");
					$sub_ficheros = $query->getResult();					
					
					//BUCLE QUE RECORRE FICHERO A FICHERO DENTRO DE LA BD PARA BORRAR SUBFICHEROS.
					foreach ($sub_ficheros as $clave => $valor){
						$filesize_total_restar+=$sub_ficheros[$clave]->getFilesize();						
						//EVENTOS (fichero o carpeta) Nota: desactivado por defecto como comentario, para no saturar el listado de eventos.	
						/*
						$eventos=new Eventos();
						$eventos->setIdUser($userid);
						$eventos->setIdFichero($sub_ficheros[$clave]->getidFichero());
						$eventos->setNombreFicheroAntiguo($sub_ficheros[$clave]->getnombreFichero());
						$eventos->setNombreFicheroNuevo($sub_ficheros[$clave]->getnombreFichero());
						$eventos->setFecha(new \Datetime(null,new \DateTimeZone("UTC")));
						$eventos->setRuta($sub_ficheros[$clave]->getRuta());
						if($sub_ficheros[$clave]->getTipo()=='fichero'){
							$eventos->setaccion("Has borrado el fichero ".$sub_ficheros[$clave]->getnombreFichero());
							$eventos->setTipo("50");
						}else{
							$eventos->setaccion("Has borrado la carpeta ".$sub_ficheros[$clave]->getnombreFichero());
							$eventos->setTipo("51");
						}
						*/
						$em->remove($sub_ficheros[$clave]);
						//$em->persist($eventos);
						$em->flush();
						
					}
					
					//Eliminar de la BD la carpeta principal y crear evento de borrado de esa carpeta.
					$eventos=new Eventos();
					$eventos->setIdUser($userid);
					$eventos->setaccion("Has borrado la carpeta ".$ficheros->getnombreFichero());
					$eventos->setTipo("51");
					$eventos->setIdFichero($ficheros->getidFichero());
					$eventos->setNombreFicheroAntiguo($ficheros->getnombreFichero());
					$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
					$eventos->setFecha(new \Datetime(null,new \DateTimeZone("UTC")));
					$eventos->setRuta($ficheros->getRuta());
					
					$em->remove($ficheros);
					$em->persist($eventos);
					
					//Restar espacio ocupado a la cuenta del userid.
					$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser'=>$userid));
					$usuarios->setOcupado($usuarios->getOcupado()-$filesize_total_restar);
					$em->flush();
					
					//BORRAR DIRECTORIO
					function deleteDirectory($dir) {
						if (!file_exists($dir)) return true;
						if (!is_dir($dir) || is_link($dir)) return unlink($dir);
							foreach (scandir($dir) as $item) {
								if ($item == '.' || $item == '..') continue;
								if (!deleteDirectory($dir . "/" . $item)) {
									chmod($dir . "/" . $item, 0777);
									if (!deleteDirectory($dir . "/" . $item)) return false;
								};
							}
						return rmdir($dir);
					}
					deleteDirectory($ruta_local);
				}
				
				return $response = New Response($codigo);
			}
			return new Response("Metodo GET no permitido");
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}

}
