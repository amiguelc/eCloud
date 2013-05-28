<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Entity\Ficheros;
use eCloud\UsuarioBundle\Entity\Eventos;
use eCloud\UsuarioBundle\Form\Frontend\UsuarioType;
//use Symfony\Component\HttpFoundation\BinaryFileResponse;
//use Symfony\Component\HttpFoundation\ResponseHeaderBag;

//use eCloud\UsuarioBundle\Form\Frontend\SubirFicheroType;


class CuentaController extends Controller{
    public function perfilAction(){
        // Obtener los datos del usuario logueado y utilizarlos para
		// rellenar un formulario de registro.
		//
		// Si la petición es GET, mostrar el formulario
		// Si la petición es POST, actualizar la información del usuario con
		// los nuevos datos obtenidos del formulario

		 if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			//$usuario = $this->get('security.context')->getToken()->getUser();
			$em = $this->getDoctrine()->getManager();
			$entity_usuarios=new Usuarios();
			$usuario=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser' => $userid));
			$formulario=$this->createFormBuilder($entity_usuarios)->add('nombre','text')->add('apellidos','text')->add('email','text')->add('nombre_usuario','text')->add('direccion','text')->add('ciudad','text')->add('pais','text')->getForm();
			
			//$formulario = $this->createFormBuilder($document)->add('nombrefichero','text', array('data'=> $ficheros2->getNombreFichero()))->add('ruta','text',array ('data'=> $ficheros2->getRuta()))->getForm();
			//$formulario = $this->createForm(new UsuarioType(), $usuario);
			

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
					//$usuario_modificado->setIdUser($userid);
					
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
				$formulario=$this->createFormBuilder($usuario)->add('nombre','text')->add('apellidos','text')->add('email','text')->add('nombre_usuario','text')->add('direccion','text')->add('ciudad','text')->add('pais','text')->getForm();
				return $this->render('UsuarioBundle:Cuenta:perfil.html.twig', array('usuario'=>$usuario,'formulario' => $formulario->createView()));
			}
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		public function ficherosAction($ruta){
		
		//leer los ficheros de la base de datos y pasarselos a twig con ajax, aqui se complica mucho la cosa
		//quizas con codigo javascript se pueda subir algun fichero
		
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
			}
			
			
			
			return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig',array('ficheros' => $ficheros,'ruta' => $ruta, 'formulario' => $formulario->createView(), 'formulario_carpeta' => $formulario_carpeta->createView()));
			
			}
			else{
					return $this->redirect($this->generateUrl('login'), 301);
			}
		
		}
		
		public function subirAction(){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
				$ficheros = new Ficheros();
				//$formulario = $this->createFormBuilder($ficheros)->add('file','file')->getForm();
				//$data = $this->getRequest()->request->all();
				//$name = $data['form']['nombrefichero'];
				if(isset($_POST['form']['nombrefichero'])){$formulario  = $this->createFormBuilder($ficheros)->add('nombrefichero','text')->add('ruta','hidden')->getForm();}
				else{$formulario = $this->createFormBuilder($ficheros)->add('file','file')->add('ruta','hidden')->getForm();}
				
				if ($this->getRequest()->isMethod('POST')) {
				
				//return $response = New Response(print_r($this->getRequest()->request->all()), true);
				//Falta comprobar si espacio lleno, en el post y en el get al subir ficheros.
				
				$formulario->bind($this->getRequest());
					if ($formulario->isValid()) {
						$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
						
						$em = $this->getDoctrine()->getManager();
								
						
						//Ficheros normales///
						//propietario, nombre_fichero, nombre_real_fisico, tipo, ruta, filesize, checksum, fecha_subida, total_descargas, permiso
						//$ficheros->setPropietario($this->get('security.context')->getToken()->getUsername());
						$ficheros->setPropietario($userid);
						
						//$ficheros->setRuta("/");
						$ruta=$formulario["ruta"]->getData();
						//return  $response = new Response(print_r($formulario["ruta"]->getData(), true));
						$ficheros->setRuta($ruta);
						$ficheros->setfechaSubida(new \Datetime());
						$ficheros->settotalDescargas("0");
						$ficheros->setPermiso("si");

						//Si es carpeta o fichero//
						if(isset($_POST['form']['nombrefichero'])){
							$ficheros->setNombreFichero($_POST['form']['nombrefichero']);
							$ficheros->setNombreRealFisico($_POST['form']['nombrefichero']);
							$ficheros->setTipo("carpeta");
							$ficheros->setMime('carpeta');
							$ficheros->setChecksum('0');
							$ficheros->setFilesize('0');
							//crear carpeta en el caso de crear carpetas
							if(isset($_POST['form']['nombrefichero'])){mkdir("C:\\ecloud\\".$userid."\\".$ficheros->getnombreFichero());}
						}else {
							$ficheros->setTipo("fichero");
							$ficheros->upload();
						}
						
						//Eventos//
						$eventos=new Eventos();
						//id_evento,id_user,accion,id_fichero,nombre_fichero_antiguo,nombre_fichero_nuevo,fecha 
						$eventos->setIdUser($userid);
						$eventos->setaccion("Has subido el fichero ".$ficheros->getnombreFichero());
						//$eventos->setIdFichero($ficheros->getIdFichero());
						$eventos->setIdFichero(0); //Falta: Problema es que el idFichero se genera despues con el autoincremento de SQL.
						$eventos->setNombreFicheroAntiguo($ficheros->getnombreFichero());
						$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
						$eventos->setFecha(new \Datetime());
						
						//Sumar espacio ocupado a la cuenta del userid, pero antes hay que comprobar que no supera el espacio.
						$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser'=>$userid));
						if($usuarios->getOcupado()+$ficheros->getFilesize()<=$usuarios->getLimite()){$usuarios->setOcupado($usuarios->getOcupado()+$ficheros->getFilesize());}else {return $this->redirect($this->generateUrl('ficheros'), 303);}
						
						//ejecutar sentencias
						$em->persist($ficheros);
						$em->persist($eventos);
						$em->persist($usuarios);
						$em->flush();
						
						
						
						//return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig',array('ruta'=>$ruta));
						return $this->redirect($this->getRequest()->headers->get('referer'),303);
					}
				}
				return $this->render('UsuarioBundle:Cuenta:subir.html.twig', array('formulario' => $formulario->createView()));
				
				
				
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
					
					//propietario, nombre_fichero, nombre_real_fisico, tipo, ruta, filesize, checksum, fecha_subida, total_descargas, permiso
					$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
					$nombre_antiguo=$ficheros->getNombreFichero();
					$nombre_nuevo=$formulario["nombrefichero"]->getData();
					$ruta_nueva=$formulario["ruta"]->getData();
					$ruta_absoluta_antigua="C:\\ecloud\\".$userid.$ficheros->getRuta()."\\".$nombre_antiguo;
					$ruta_absoluta_nueva="C:\\ecloud\\".$userid.$ficheros->getRuta()."\\".$nombre_nuevo;
					
					//$ficheros->setRuta($ruta_nueva);
					$ficheros->setNombreFichero($nombre_nuevo);
					$ficheros->setNombreRealFisico($nombre_nuevo);
					
					$em->persist($ficheros);
					$em->flush();
					
					//falta modificar nombre fichero.
					rename($ruta_absoluta_antigua,$ruta_absoluta_nueva);
					return $this->redirect($this->generateUrl('ficheros'),303);
					//return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
				}
			}
			else{
				//mostrar datos y formulario
				$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
				$ficheros = new Ficheros();
				//ver fichero o carpeta y modificar datos.
				$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findBy(array('idFichero'=>$fichero,'propietario' => $userid));
				$ficheros2=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
				//id_fichero,propietario,nombre_fichero,nombre_real_fisico,ruta,filesize,checksum,fecha_subida,total_descargas,permiso,tipo 
								
				$formulario = $this->createFormBuilder($document)->add('nombrefichero','text', array('data'=> $ficheros2->getNombreFichero()))->add('ruta','text',array ('data'=> $ficheros2->getRuta()))->getForm();
			return $this->render('UsuarioBundle:Cuenta:modificar.html.twig',array('fichero'=>$fichero,'ficheros' => $ficheros,'formulario'=>$formulario->createView()));
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
				$ruta_local="C:\\ecloud\\".$userid.print_r($ficheros->getRuta(), true).print_r($ficheros->getNombreFichero(), true);
				$ruta_local=str_replace("/", "\\", $ruta_local);
				
				//Eventos//
				//falta restar espacio a la cuenta del userid y evento
				$eventos=new Eventos();
				//id_evento,id_user,accion,id_fichero,nombre_fichero_antiguo,nombre_fichero_nuevo,fecha 
				$eventos->setIdUser($userid);
				$eventos->setaccion("Has borrado el fichero ".$ficheros->getnombreFichero());
				//$eventos->setIdFichero($document->getIdFichero());
				$eventos->setIdFichero(0); //Falta: Problema es que el idFichero se genera despues con el autoincremento de SQL.
				$eventos->setNombreFicheroAntiguo($ficheros->getnombreFichero());
				$eventos->setNombreFicheroNuevo($ficheros->getnombreFichero());
				$eventos->setFecha(new \Datetime());
				
				//Restar espacio ocupado a la cuenta del userid.
				$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser'=>$userid));
				$usuarios->setOcupado($usuarios->getOcupado()-$ficheros->getFilesize());
				
				//ejecutar sentencias
				$em->persist($eventos);
				$em->persist($usuarios);
				$em->remove($ficheros);			
				$em->flush();
				//falta borrar el fichero del directorio, si es fichero o carpeta
				if (file_exists($ruta_local)){unlink($ruta_local);}
				return $this->redirect($this->generateUrl('ficheros'), 303);
				//return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
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
				$ruta_local="C:\\ecloud\\".$userid.print_r($ficheros->getRuta(), true)."\\".print_r($ficheros->getNombreFichero(), true);
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
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em=$this->getDoctrine()->getManager();
			$eventos=$em->getRepository('UsuarioBundle:Eventos')->findBy(array('idUser' => $userid),array('fecha' => 'DESC'));
			
			//mostrar lista de modificaciones sin mas.
			
			
			
			return $this->render('UsuarioBundle:Cuenta:eventos.html.twig', array('eventos'=>$eventos));
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		public function linksAction(){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			//
			//si GET, ver lista de LINKS, si link especificado, mostrar formulario para ver y modificarlo.
			//
			if ($this->getRequest()->isMethod('POST')){
			//Guardar link en BD.
			
			
			}
			else{
			//GET a secas-> Ver lista de links, GET con idFichero y formulario para editar.
				if ($this->getRequest()->isMethod('POST')){}
			}

			return $this->render('UsuarioBundle:Cuenta:links.html.twig');
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
}