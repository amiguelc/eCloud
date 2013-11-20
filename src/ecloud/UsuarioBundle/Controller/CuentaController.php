<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Entity\Ficheros;
use eCloud\UsuarioBundle\Entity\Eventos;
use eCloud\UsuarioBundle\Entity\Enlaces;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Validator\Constraints\NotBlank;



class CuentaController extends Controller{
    public function perfilAction(){
        // Obtener los datos del usuario logueado y utilizarlos para rellenar un formulario de registro.
		//
		// Si la petición es GET, mostrar el formulario
		// Si la petición es POST, actualizar la información del usuario con los nuevos datos obtenidos del formulario

		 if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em = $this->getDoctrine()->getManager();
			$entity_usuarios=new Usuarios();
			$usuario=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser' => $userid));
			$formulario=$this->createFormBuilder($entity_usuarios)->add('nombre','text')->add('apellidos','text')->add('email','text')->add('nombre_usuario','text')->add('direccion','text')->add('ciudad','text')->add('pais','text')->getForm();

			if ($this->getRequest()->isMethod('POST')) {
				$formulario->bind($this->getRequest());
				if ($formulario->isValid()) {
					// actualizar el perfil del usuario
					$usuario=new Usuarios();
					$usuario=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser' => $userid));
					$usuario->setNombre($formulario["nombre"]->getData());
					$usuario->setApellidos($formulario["apellidos"]->getData());
					$usuario->setEmail($formulario["email"]->getData());
					$usuario->setNombreUsuario($formulario["nombre_usuario"]->getData());
					$usuario->setDireccion($formulario["direccion"]->getData());
					$usuario->setCiudad($formulario["ciudad"]->getData());
					$usuario->setPais($formulario["pais"]->getData());
					
					//$formulario_recibido=setIpRegistro($this->getIpRegistro());
					//$formulario_recibido=setLimite('5555555');
					//$formulario_recibido=setPassword($this->getPassword());
					
					$em->persist($usuario);
					$em->flush();
					
					return $this->redirect($this->generateUrl('perfil'), 303);
				}
				else {
				return $this->redirect($this->generateUrl('perfil'), 303);
				}
			}
			else{
				//$formulario = $this->createFormBuilder($usuario)->add('nombrefichero','text', array('data'=> $ficheros2->getNombreFichero()))->add('ruta','text',array ('data'=> $ficheros2->getRuta()))->getForm();
				$usuario->setLimite(round(($usuario->getLimite()/1024/1024),2)." MB");
				$usuario->setOcupado(round(($usuario->getOcupado()/1024/1024),2));
				$usuario->setOcupado($usuario->getOcupado()." MB ");
				$libre=($usuario->getLimite()-$usuario->getOcupado())." MB (".round((($usuario->getOcupado()/$usuario->getLimite())*100),2)."%)";
				$formulario=$this->createFormBuilder($usuario)->add('nombre','text')->add('apellidos','text')->add('email','text')->add('nombre_usuario','text')->add('direccion','text')->add('ciudad','text')->add('pais','text')->getForm();
				return $this->render('UsuarioBundle:Cuenta:perfil.html.twig', array('usuario'=>$usuario,'formulario' => $formulario->createView(), 'libre'=> $libre));
			}
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		public function perfiljsonAction($info){
 
		 if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
		

			if ($this->getRequest()->isMethod('POST')) {
			}
			else{
				$em=$this->getDoctrine()->getManager();
				$usuario=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser' => $userid));
				//return new Response (var_dump($usuario));
				//if isset nombre dar solo esa info-> jsonificar
				
				if ($info!="all"){
				//Enviar solo lo pedido [email,nombreusuario,limite,ocupado] sin JSON

					switch ($info){
					case "email":
						$jsonContent=$usuario->getEmail();
						break;
					case "nombreusuario":
						$jsonContent=$usuario->getNombreUsuario();
						break;
					case "limite":
						$jsonContent=$usuario->getLimite();
						break;
					case "ocupado":
						$jsonContent=$usuario->getOcupado();
						break;
					case "libre":
						$jsonContent=$usuario->getLimite()-$usuario->getOcupado();
						break;
					case "espacio":
						$libre=$usuario->getLimite()-$usuario->getOcupado();
						$jsonContent="{\"limite\":\"".$usuario->getLimite()."\",\"ocupado\":\"".$usuario->getOcupado()."\", \"libre\":\"".$libre."\"}";
						break;
					default:
						//Envia todo... hasta contraseña..
						$usuario->setPassword("");
						$encoders = array(new XmlEncoder(), new JsonEncoder());
						$normalizers = array(new GetSetMethodNormalizer());
						$serializer = new Serializer($normalizers, $encoders);
						$jsonContent = $serializer->serialize($usuario, 'json');
						break;
					}
					
				
				}else{
				//Enviar todo en formato JSON
				$usuario->setPassword("");
				$encoders = array(new XmlEncoder(), new JsonEncoder());
				$normalizers = array(new GetSetMethodNormalizer());
				$serializer = new Serializer($normalizers, $encoders);
				$jsonContent = $serializer->serialize($usuario, 'json');
				}
				
			

				return new Response ($jsonContent);
					
					
			}
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		public function ficherosAction($ruta){
		
		//falta ajaxificar
		//con codigo javascript a la hora de subir ficheros tener en cuenta el espacio libre y en base a ello permitir la subida del fichero, calcular checksum.
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			
			if ($this->getRequest()->isMethod('POST')) {
				//guardar fichero y tal
			}else{
				//coger ficheros de ese usuario y pasarlos a la plantilla twig, primero saber que ruta pide.
				$entity_ficheros = new Ficheros();
				//quitar la / de la ruta
				if(!isset($ruta)){$ruta="";}
				//quitar / del principio
				if($ruta[0]=="/"){$ruta=substr($ruta,1);}
				//quitar / del final
				if(($ruta[(strlen($ruta))-1])=="/"){$ruta=substr($ruta,0,-1);}
				
				//formulario de subir fichero
				$formulario = $this->createFormBuilder($entity_ficheros)->add('file','file',array('label'=>'Archivo '))->add('ruta','hidden',array('data' => "/".$ruta))->getForm();
				$formulario_carpeta  = $this->createFormBuilder($entity_ficheros)->add('nombrefichero','text',array('label'=>'Carpeta'))->add('ruta','hidden',array('data' => "/".$ruta))->getForm();
				$ficheros=$this->getDoctrine()->getManager()->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid, 'ruta' => "/".$ruta));
				
				//For each $ficheros cuando sea carpeta hacer una SQL para sacar el tamaño de subficheros. Esto consumirá mucho SQL. Ademas no se deberia poner aqui, sino en ficheros JSON y guardarse el valor para la app.
				$filesize_total=0;

				foreach ($ficheros as $clave => $valor){
					if ($ficheros[$clave]->getTipo()=="carpeta"){
					
						if($ficheros[$clave]->getRuta()=="/"){$carpeta_en_cuestion="/".$ficheros[$clave]->getNombreFichero();}
						else{$carpeta_en_cuestion=$ficheros[$clave]->getRuta()."/".$ficheros[$clave]->getNombreFichero();}
						
					$em=$this->getDoctrine()->getManager();
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
				
				//return $response = New Response(var_dump($ficheros));
				//PASAR BYTES A MB
				
				foreach ($ficheros as $clave => $valor){
				$ficheros[$clave]->setFilesize(round(($ficheros[$clave]->getFilesize()/1024/1024),2));
				}
				
			}
			
			
			
			return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig',array('ficheros' => $ficheros,'ruta' => $ruta, 'formulario' => $formulario->createView(), 'formulario_carpeta' => $formulario_carpeta->createView()));
			
			}
			else{
					return $this->redirect($this->generateUrl('login'), 301);
			}
		
		}
		
		public function ficherosjsonAction(){
		
		//leer los ficheros de la base de datos y pasarselos a twig con ajax, aqui se complica mucho la cosa
		//quizas con codigo javascript se pueda subir algun fichero
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			
			if ($this->getRequest()->isMethod('POST')) {
			}else{
				$ficheros=$this->getDoctrine()->getManager()->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid));
				
				//PASAR BYTES A MB
				
				foreach ($ficheros as $clave => $valor){
				$ficheros[$clave]->setFilesize(round(($ficheros[$clave]->getFilesize()/1024/1024),2));
				}
				
			}
			
			//JSON
			$encoders = array(new XmlEncoder(), new JsonEncoder());
			$normalizers = array(new GetSetMethodNormalizer());
			$serializer = new Serializer($normalizers, $encoders);		
			$jsonContent = $serializer->serialize($ficheros, 'json');
			return new Response ($jsonContent);
			//JSON
			
			}
			else{
					return $this->redirect($this->generateUrl('login'), 301);
			}
		
		}
		
		
		public function ficheroAction($id){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			
			//Validar ID.
			
			$fichero = new Ficheros();
			$fichero=$this->getDoctrine()->getManager()->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid, 'idFichero' => $id));
			if ($fichero==NULL){return new Response ("Fichero no encontrado");}
				
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
			//JSON
			
			}
			else{
					return $this->redirect($this->generateUrl('login'), 301);
			}
		
		}
		
		public function subirAction(){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
				$ficheros = new Ficheros();
				//$data = $this->getRequest()->request->all();
				//$name = $data['form']['nombrefichero'];
				
				$carpeta=FALSE;
				if(isset($_POST['form']['nombrefichero'])){$carpeta=TRUE;$formulario  = $this->createFormBuilder($ficheros)->add('nombrefichero','text')->add('ruta','hidden', array('constraints' => new NotBlank()))->getForm();
				}
				else{$formulario = $this->createFormBuilder($ficheros)->add('file','file')->add('ruta','hidden', array('constraints' => new NotBlank()))->getForm();
				}
				
				if ($this->getRequest()->isMethod('POST')) {
				
				$formulario->handleRequest($this->getRequest());
					if ($formulario->isValid()) {
						$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
						
						$em = $this->getDoctrine()->getManager();
							
						//Comprobaciones de nombre de fichero, carpeta o ruta. Si el fichero/carpeta ya existe. Caracteres prohibidos ( / \ : ? < > ' " ~ * | )
						
						if($carpeta==TRUE){
						//Comprobaciones a carpeta.
							if(preg_match("/(\/|\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['nombrefichero'])==1){ return  $response = new Response("Error en el nombre de la carpeta");}
							if(preg_match("/(\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['ruta'])==1){ return  $response = new Response("Error en la ruta de la carpeta");}
							if(preg_match("/\.\.\//",$_POST['form']['ruta'])==1){ return  $response = new Response("No se permite rutas con ../");}
							
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
						}
						else{
						//Comprobaciones a fichero			
							if(preg_match("/(\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['ruta'])==1){ return  $response = new Response("Error en la ruta del fichero");}
							if(preg_match("/\.\.\//",$_POST['form']['ruta'])==1){ return  $response = new Response("No se permite rutas con ../");}
							if(preg_match("/(\/|\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_FILES['form']['name']['file'])==1){ return  $response = new Response("Error en el nombre del fichero");}
							
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
						}
						
						//Ficheros normales///
						//propietario, nombre_fichero, nombre_real_fisico, tipo, ruta, filesize, checksum, fecha_subida, total_descargas, permiso
						$ficheros->setPropietario($userid);
						
						//$ficheros->setRuta("/");
						$ruta=$formulario["ruta"]->getData();
						$ficheros->setRuta($ruta);
						$ficheros->setfechaSubida(new \Datetime());
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
							if($carpeta==TRUE){mkdir($this->container->getParameter('var_archivos').$userid.$ficheros->getRuta()."\\".$ficheros->getnombreFichero());}
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
						if ($carpeta==FALSE){$eventos->setaccion("Has subido el fichero ".$ficheros->getnombreFichero());}else{$eventos->setaccion("Has creado la carpeta ".$ficheros->getnombreFichero()." en ".$ficheros->getRuta());}
						$eventos->setIdFichero($ficheros->getIdFichero());
						$eventos->setNombreFicheroAntiguo($ficheros->getnombreFichero());
						$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
						$eventos->setFecha(new \Datetime());
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

		public function modificarAction($fichero){
       
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			
			$document = new Ficheros();
			$formulario = $this->createFormBuilder($document)->add('nombrefichero','text')->add('ruta','text')->getForm();
			$em = $this->getDoctrine()->getManager();
			
			
			if ($this->getRequest()->isMethod('POST')) {
				//guardar datos modificados
				$formulario->bind($this->getRequest());
				if ($formulario->isValid()) {
									
					//Validar datos.
					if($_POST['form']['ruta']=="" || $_POST['form']['nombrefichero']==""){ return  $response = new Response("Faltan datos");}
					if(preg_match("/(\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['ruta'])==1){ return  $response = new Response("Error en la ruta del fichero");}
					if(preg_match("/\.\.\//",$_POST['form']['ruta'])==1){ return  $response = new Response("No se permite rutas con ../");}
					if(preg_match("/(\/|\\\\|:|\?|<|>|\'|\"|~|\*|\|)/",$_POST['form']['nombrefichero'])==1){ return  $response = new Response("Error en el nombre del fichero");}
					
					$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
					if ($ficheros==null){ return  $response = new Response("Ese fichero no es tuyo");}
					
					if($_POST['form']['nombrefichero']==$ficheros->getNombreFichero() && $_POST['form']['ruta']==$ficheros->getRuta()){return $this->redirect($this->generateUrl('ficheros'),303);} //Ningun cambio
					elseif($_POST['form']['nombrefichero']==$ficheros->getNombreFichero() && $_POST['form']['ruta']!=$ficheros->getRuta()){$accion="mover";} //Mover fichero, comprobar que la nueva carpeta existe y que en ella no exista la misma carpeta o fichero.
					elseif($_POST['form']['nombrefichero']!=$ficheros->getNombreFichero() && $_POST['form']['ruta']!=$ficheros->getRuta()){$accion="mover y cambiar";} //Comprobar si carpeta nueva existe, si fichero existe y evento has modificado el nombre y movido a...
					elseif($_POST['form']['nombrefichero']!=$ficheros->getNombreFichero() && $_POST['form']['ruta']==$ficheros->getRuta()){$accion="cambiar";}//Has cambiado de nombre..Comprobar si fichero ya existe.
					else{ return $response=new Response("Error");}
					
					$nombre_antiguo=$ficheros->getNombreFichero();
					$nombre_nuevo=$formulario["nombrefichero"]->getData();
					$ruta_nueva=$formulario["ruta"]->getData();	
					if (strlen($ruta_nueva)>1 && $ruta_nueva[strlen($ruta_nueva)-1]=="/"){$ruta_nueva=substr($ruta_nueva, 0, -1);}//Para quitar el ultimo / de la ruta si lo tuviera. Cuidao con la raiz
					$ruta_antigua=$ficheros->getRuta();
					$ruta_absoluta_antigua=$this->container->getParameter('var_archivos').$userid.$ficheros->getRuta()."\\".$nombre_antiguo;
					$ruta_absoluta_nueva=$this->container->getParameter('var_archivos').$userid.$ruta_nueva."\\".$nombre_nuevo;
					
					$ficheros->setRuta($ruta_nueva);
					$ficheros->setNombreFichero($nombre_nuevo);
					$ficheros->setNombreRealFisico($nombre_nuevo);
										
					//Eventos//
					$eventos=new Eventos();
					$eventos->setIdUser($userid);
					
					if($accion=="cambiar"){
						if($ficheros->getTipo()=='fichero'){
							$eventos->setaccion("Has cambiado el nombre al fichero ".$nombre_antiguo." por ".$nombre_nuevo);
						}
						else{
							$eventos->setaccion("Has cambiado el nombre a la carpeta ".$nombre_antiguo." por ".$nombre_nuevo);
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
						}
						else{
							$eventos->setaccion("Has movido la carpeta ".$nombre_nuevo." a ".$ruta_nueva);
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
						}
						else{
							$eventos->setaccion("Has cambiado el nombre a la carpeta ".$nombre_antiguo." por ".$nombre_nuevo." y lo has movido a ".$ruta_nueva);
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
					$eventos->setFecha(new \Datetime());
					$eventos->setRuta($ficheros->getRuta());
					
			
					if($ficheros->getTipo()=='carpeta'){
					
					//Cambia ruta subficheros.
					$query = $em->createQuery('UPDATE UsuarioBundle:Ficheros f SET f.ruta = ?1 WHERE f.ruta LIKE ?2 and f.propietario=?3');
					if($ruta_nueva=="/"){$query->setParameter(1, "/".$nombre_nuevo);}else{$query->setParameter(1, $ruta_nueva."/".$nombre_nuevo);}
					if ($ruta_antigua=="/"){$query->setParameter(2, $ruta_antigua.$nombre_antiguo);}else{$query->setParameter(2, $ruta_antigua."/".$nombre_antiguo);}
					$query->setParameter(3, $userid);
					$query->getResult();
					
					
					//Saca las rutas de los sub-subficheros a cambiar
					$query = $em->createQuery('SELECT f.ruta FROM UsuarioBundle:Ficheros f WHERE f.ruta LIKE ?1');
					if ($ruta_antigua=="/"){$query->setParameter(1, $ruta_antigua.$nombre_antiguo.'/%');}else{$query->setParameter(1, $ruta_antigua."/".$nombre_antiguo.'/%');}
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
					
					//Detectar SO y preparar ruta para ello.
					if (PHP_OS=="WINNT"){
					
					}else{
					//Convierte rutas windows a unix. Codigo no probado.
					$ruta_absoluta_antigua=preg_replace(":\:","/",$ruta_absoluta_antigua);
					$ruta_absoluta_nueva=preg_replace(":\:","/",$ruta_absoluta_nueva);
					}
					rename($ruta_absoluta_antigua,$ruta_absoluta_nueva);
										
					return $this->redirect($this->generateUrl('ficheros'),303);
				}
			}
			else{
				//mostrar datos y formulario, incluye el de crear link
				$ficheros = new Ficheros();
				//ver fichero o carpeta y modificar datos.
				$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findBy(array('idFichero'=>$fichero,'propietario' => $userid));
				$ficheros2=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
				//id_fichero,propietario,nombre_fichero,nombre_real_fisico,ruta,filesize,checksum,fecha_subida,total_descargas,permiso,tipo 
								
				$formulario = $this->createFormBuilder($document)->add('nombrefichero','text', array('data'=> $ficheros2->getNombreFichero()))->add('ruta','text',array ('data'=> $ficheros2->getRuta()))->getForm();
				
				//LINK
				//idEnlace,url,idFichero,propietario,fechaInicio,fechaExpiracion,usuarios(anonimos,registrados)
				$entity_enlace=new Enlaces();
				$formulario_link = $this->createFormBuilder($entity_enlace)->add('url','text')->add('idFichero','hidden',array ('data'=> $fichero))->add('propietario','hidden',array ('data'=> $userid))->add('fechaInicio','date')->add('fechaExpiracion','date')->add('usuarios','choice', array('choices' => array('todos' => 'Todos', 'registrados' => 'Solo registrados')))->getForm();
				
			return $this->render('UsuarioBundle:Cuenta:modificar.html.twig',array('fichero'=>$fichero,'ficheros' => $ficheros,'formulario'=>$formulario->createView(),'formulario_link'=>$formulario_link->createView()));
			}
			
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		public function borrarAction($fichero){
       
			if ($this->get('security.context')->isGranted('ROLE_USER')){
				$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
				$em=$this->getDoctrine()->getManager();
				$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
				if ($ficheros==null){ return  $response = new Response("Ese fichero no es tuyo");}
				$ruta_local=$this->container->getParameter('var_archivos').$userid.print_r($ficheros->getRuta(), true).print_r($ficheros->getNombreFichero(), true);
				$ruta_local=str_replace("/", "\\", $ruta_local);
				
				
				//Si es fichero
				if ($ficheros->getTipo()=="fichero"){
					//Eventos//
					$eventos=new Eventos();
					//id_evento,id_user,accion,id_fichero,nombre_fichero_antiguo,nombre_fichero_nuevo,fecha 
					$eventos->setIdUser($userid);
					$eventos->setaccion("Has borrado el fichero ".$ficheros->getnombreFichero());
					//$eventos->setIdFichero($document->getIdFichero());
					$eventos->setIdFichero(0); //Falta: Problema es que el idFichero se genera despues con el autoincremento de SQL.
					$eventos->setNombreFicheroAntiguo($ficheros->getnombreFichero());
					$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
					$eventos->setFecha(new \Datetime());
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
				//$sub_ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findByRuta(array('idFichero'=>$fichero,'propietario' => $userid));
				$filesize_total_restar=0;
				$ruta_carpeta=str_replace('//','/',$ficheros->getRuta()."/".$ficheros->getNombreFichero());
				$query=$em->createQuery('SELECT f FROM UsuarioBundle:Ficheros f WHERE f.propietario=?1 and f.ruta like ?2 ORDER BY f.tipo DESC');
				$query->setParameter(1, $userid);
				$query->setParameter(2, $ruta_carpeta."%");
				$sub_ficheros = $query->getResult();
				//return $response = New Response(var_dump($sub_ficheros));
				
				
				//BUCLE QUE RECORRE FICHERO A FICHERO DENTRO DE LA BD PARA BORRAR SUBFICHEROS
				foreach ($sub_ficheros as $clave => $valor){
				
				//Falta analizar el sentido de esta SQL. Creo que hay que eliminarla.
				$filesize_query=$em->createQuery('SELECT f from UsuarioBundle:Ficheros f WHERE f.propietario=?1 and id_fichero=?2');
				$filesize_query->setParameter(1, $userid);
				$filesize_query->setParameter(2, $sub_ficheros[$clave]->getidFichero());
				$filesize_query = $query->getResult();

				//$filesize_f=$filesize_query[$clave]->getFilesize();
				$filesize_total_restar+=$sub_ficheros[$clave]->getFilesize();
				
				//EVENTOS (fichero o carpeta)
				$eventos=new Eventos();
				//id_evento,id_user,accion,id_fichero,nombre_fichero_antiguo,nombre_fichero_nuevo,fecha 
				$eventos->setIdUser($userid);
				//$eventos->setIdFichero($document->getIdFichero());
				$eventos->setIdFichero(0); //Falta: Problema es que el idFichero se genera despues con el autoincremento de SQL.
				$eventos->setNombreFicheroAntiguo($sub_ficheros[$clave]->getnombreFichero());
				$eventos->setNombreFicheroNuevo($sub_ficheros[$clave]->getnombreFichero());
				$eventos->setFecha(new \Datetime());
				$eventos->setRuta($sub_ficheros[$clave]->getRuta());
				if($sub_ficheros[$clave]->getTipo()=='fichero'){
				$eventos->setaccion("Has borrado el fichero ".$sub_ficheros[$clave]->getnombreFichero());
				}else{
				$eventos->setaccion("Has borrado la carpeta ".$sub_ficheros[$clave]->getnombreFichero());
				}
				
				$em->remove($sub_ficheros[$clave]);
				$em->persist($eventos);
				$em->flush();
				
				
				//return $response = New Response(var_dump($filesize_query));
				}//cierra foreach
				
				//Eliminar de la BD la carpeta principal y crear evento de borrado de esa carpeta.
				$eventos=new Eventos();
				//id_evento,id_user,accion,id_fichero,nombre_fichero_antiguo,nombre_fichero_nuevo,fecha 
				$eventos->setIdUser($userid);
				$eventos->setaccion("Has borrado la carpeta ".$ficheros->getnombreFichero());
				//$eventos->setIdFichero($document->getIdFichero());
				$eventos->setIdFichero(0); //Falta: Problema es que el idFichero se genera despues con el autoincremento de SQL.
				$eventos->setNombreFicheroAntiguo($ficheros->getnombreFichero());
				$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
				$eventos->setFecha(new \Datetime());
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
				
				//$sub_ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid,'ruta'=>$ruta_carpeta));
				//return $response = New Response(var_dump($usuarios));
				
				
				
				
				
				}
				
				
				
				
				//Con el borrado por ajax devuelve 1 en casa de borrado bien y 0 en caso de que no

				return $response = New Response('1');

			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
				
		public function descargarAction($fichero){
       
			if ($this->get('security.context')->isGranted('ROLE_USER')){
				$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
				$em=$this->getDoctrine()->getManager();
				$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
				if (!$ficheros) {throw $this->createNotFoundException('No existe el fichero '.$fichero);}
				
				//procesar ruta y descargar el archivo, sumar una descarga a la BD.
				$ruta_local=$this->container->getParameter('var_archivos').$userid.print_r($ficheros->getRuta(), true)."\\".print_r($ficheros->getNombreFichero(), true);
				$ruta_local=str_replace("/", "\\", $ruta_local);
				$ficheros->setTotalDescargas($ficheros->getTotalDescargas()+1);
				$mime=$ficheros->getMime();
				//sumar descarga a la bd
				$em->flush();
				
				//servir archivo
				$headers = array('Content-Type' => $ficheros->getMime(),'Content-Disposition' => 'attachment; filename="'.$ficheros->getNombreFichero().'"');  
				
				return new Response(file_get_contents($ruta_local), 200, $headers);


				//return $this->redirect($this->generateUrl('ficheros'), 303);
				//return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
			}
			else{
			
				//falta mostrar descarga a usuarios publicos, los links apuntaran aqui.
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		
		public function eventosAction(){
       
			if ($this->get('security.context')->isGranted('ROLE_USER')){

			return $this->render('UsuarioBundle:Cuenta:eventos.html.twig');
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		public function eventosJSONAction(){
       
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em=$this->getDoctrine()->getManager();
			$eventos=$em->getRepository('UsuarioBundle:Eventos')->findBy(array('idUser' => $userid),array('fecha' => 'DESC'));
	
			//Hay que devolver el resultado en JSON. Esta es la manera larga utilizando los componentes de Symfony
	/*
			$rows = array();
			while($r = $resultado->fetch_assoc()) {
				$rows[] = $r;
			}
			$eventos=json_encode($rows);
	*/
			$encoders = array(new XmlEncoder(), new JsonEncoder());
			$normalizers = array(new GetSetMethodNormalizer());

			$serializer = new Serializer($normalizers, $encoders);
			
			$jsonContent = $serializer->serialize($eventos, 'json');

			return new Response ($jsonContent);
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		public function linksAction($id){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em=$this->getDoctrine()->getManager();
			
			//idEnlace,url,idFichero,propietario,fechaInicio,fechaExpiracion,usuarios(todos,registrados)
			$entity_enlace=new Enlaces();
			//$formulario_link = $this->createFormBuilder($entity_enlace)->add('url','text')->add('idFichero','hidden',array ('data'=> $fichero))->add('propietario','hidden',array ('data'=> $userid))->add('fechaInicio','date')->add('fechaExpiracion','date')->add('usuarios','choice', array('choices' => array('todos' => 'Todos', 'registrados' => 'Solo registrados')))->getForm();
			$formulario_link = $this->createFormBuilder($entity_enlace)->add('url','text')->add('idFichero','hidden')->add('propietario','hidden')->add('fechaInicio','date')->add('fechaExpiracion','date')->add('usuarios','choice', array('choices' => array('todos' => 'Todos', 'registrados' => 'Solo registrados')))->getForm();
			//si GET, ver lista de LINKS, si link especificado, mostrar formulario para ver y modificarlo.

			if ($this->getRequest()->isMethod('POST')){
			//Guardar datos
			$formulario_link->bind($this->getRequest());
			if ($formulario_link->isValid()) {
			$enlace = $formulario_link->getData();
			$em->persist($enlace);
			$em->flush();
			return $this->redirect($this->generateUrl('links'), 303);
			}else{
			return $response = New Response(var_dump($formulario_link));
			}
			
			
			

			}
			else{
			//GET a secas-> Ver lista de links, GET con idFichero y formulario para editar.
				if($id==0){
				//Ver Lista de links.
				$enlaces=$em->getRepository('UsuarioBundle:Enlaces')->findBy(array('propietario'=>$userid));
				
				
				
				
				return $this->render('UsuarioBundle:Cuenta:links.html.twig', array('enlaces'=>$enlaces));
				}else{
				//Ver LINK especifico
				
				return $this->redirect($this->generateUrl('ficheros'), 303);
				//return $this->render('UsuarioBundle:Cuenta:links.html.twig');
				}
			}

			return $this->redirect($this->generateUrl('links'), 303);
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
				
				
		public function borrarlinkAction($id){
       
			if ($this->get('security.context')->isGranted('ROLE_USER')){
				$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
				$em=$this->getDoctrine()->getManager();
				$ficheros=$em->getRepository('UsuarioBundle:Enlaces')->findOneBy(array('idEnlace'=>$id,'propietario' => $userid));
				
				$em->remove($ficheros);
				$em->flush();
				
				return $this->redirect($this->generateUrl('links'), 303);
			
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		public function downloadAction($descarga){
       
			if ($this->get('security.context')->isGranted('ROLE_USER')){
				//$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
				$em=$this->getDoctrine()->getManager();
				$enlace=$em->getRepository('UsuarioBundle:Enlaces')->findOneBy(array('idEnlace'=>$descarga));
				if (!$enlace) {throw $this->createNotFoundException('No existe ese fichero');}
				$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$enlace->getIdFichero()));
				if (!$ficheros) {throw $this->createNotFoundException('No existe ese fichero');}
				//procesar ruta y descargar el archivo, sumar una descarga a la BD.
				$ruta_local=$this->container->getParameter('var_archivos').$ficheros->getPropietario().print_r($ficheros->getRuta(), true)."\\".print_r($ficheros->getNombreFichero(), true);
				$ruta_local=str_replace("/", "\\", $ruta_local);
				$ficheros->setTotalDescargas($ficheros->getTotalDescargas()+1);
				$mime=$ficheros->getMime();
				//sumar descarga a la bd
				$em->flush();
				
				//servir archivo
				$headers = array('Content-Type' => $ficheros->getMime(),'Content-Disposition' => 'attachment; filename="'.$ficheros->getNombreFichero().'"');  
				
				return new Response(file_get_contents($ruta_local), 200, $headers);


				//return $this->redirect($this->generateUrl('ficheros'), 303);
				//return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
			}
			else{
			
				//FALTA MOSTRAR FICHERO Y ESPERA
				$em=$this->getDoctrine()->getManager();
				$enlace=$em->getRepository('UsuarioBundle:Enlaces')->findOneBy(array('idEnlace'=>$descarga));
				if (!$enlace) {throw $this->createNotFoundException('No existe ese fichero');}
				$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$enlace->getIdFichero()));
				if (!$ficheros) {throw $this->createNotFoundException('No existe ese fichero');}
				if($enlace->getUsuarios()=='todos'){
				//procesar enlace y servir al usuario.
				
				if ($this->getRequest()->isMethod('POST')){
				//procesar ruta y descargar el archivo, sumar una descarga a la BD.
				$ruta_local=$this->container->getParameter('var_archivos').$ficheros->getPropietario().print_r($ficheros->getRuta(), true)."\\".print_r($ficheros->getNombreFichero(), true);
				$ruta_local=str_replace("/", "\\", $ruta_local);
				$ficheros->setTotalDescargas($ficheros->getTotalDescargas()+1);
				$mime=$ficheros->getMime();
				//sumar descarga a la bd
				$em->flush();
				
				//servir archivo
				$headers = array('Content-Type' => $ficheros->getMime(),'Content-Disposition' => 'attachment; filename="'.$ficheros->getNombreFichero().'"');  
				
				return new Response(file_get_contents($ruta_local), 200, $headers);
				}
				
				$ficheros->setFilesize(round($ficheros->getFilesize()/1024/1024,2));
				
				return $this->render('UsuarioBundle:Cuenta:descarga.html.twig', array('fichero'=>$ficheros,'error'=>''));
				}
				else{
				//mostrar error de solo para usuario registrados.
				
				return $this->render('UsuarioBundle:Cuenta:descarga.html.twig', array('fichero'=>$ficheros,'error'=>'Solo para usuarios registrados'));
				}
			
			
			
				//falta mostrar descarga a usuarios publicos, los links apuntaran aqui.
				
				//return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		
}