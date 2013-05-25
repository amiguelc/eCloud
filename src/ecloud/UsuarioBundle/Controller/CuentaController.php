<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Entity\Ficheros;
use eCloud\UsuarioBundle\Form\Frontend\UsuarioType;
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
			
			
			$usuario = $this->get('security.context')->getToken()->getUser();
			$formulario = $this->createForm(new UsuarioType(), $usuario);
			//$peticion = $this->getRequest();
			if ($this->getRequest()->isMethod('POST')) {
				$formulario->bindRequest($peticion);
				if ($formulario->isValid()) {
					// actualizar el perfil del usuario
				}
			}
			return $this->render('UsuarioBundle:Cuenta:perfil.html.twig', array('usuario' => $usuario,'formulario' => $formulario->createView()	));
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
				
				//quitar la / de la ruta
				if(!isset($ruta)){$ruta="";}
				//quitar / del principio
				if($ruta[0]=="/"){$ruta=substr($ruta,1);}
				//quitar / del final
				if(($ruta[(strlen($ruta))-1])=="/"){$ruta=substr($ruta,0,-1);}
				
				$ficheros=$this->getDoctrine()->getManager()->getRepository('UsuarioBundle:Ficheros')->findBy(array('propietario' => $userid, 'ruta' => "/".$ruta));
			}
			
			
			
			return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig',array('ficheros' => $ficheros,'ruta' => $ruta));
			
			}
			else{
					return $this->redirect($this->generateUrl('login'), 301);
			}
		
		}
		
		public function subirAction(){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
				$document = new Ficheros();
				$formulario = $this->createFormBuilder($document)->add('file','file')->getForm();
				if ($this->getRequest()->isMethod('POST')) {
				
				//Falta comprobar si espacio lleno, en el post y en el get al subir ficheros.
				
				$formulario->bind($this->getRequest());
					if ($formulario->isValid()) {
						$em = $this->getDoctrine()->getManager();
						//propietario, nombre_fichero, nombre_real_fisico, tipo, ruta, filesize, checksum, fecha_subida, total_descargas, permiso
						$document->setPropietario($this->get('security.context')->getToken()->getUsername());
						$document->setTipo("fichero");
						$document->setRuta("/");
						$ruta=$document->getRuta();
						$document->setfechaSubida(new \Datetime());
						$document->settotalDescargas("0");
						$document->setPermiso("si");
						$document->upload();
						$document->setPropietario($this->get('security.context')->getToken()->getUser()->getidUser());
						$em->persist($document);
						$em->flush();
						
						//falta sumar espacio a la cuenta del userid
						
						
						//return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig',array('ruta'=>$ruta));
						return $this->redirect($this->generateUrl('ficheros'));
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
			
			if ($this->getRequest()->isMethod('POST')) {
				//guardar datos modificados
				$formulario->bind($this->getRequest());
				if ($formulario->isValid()) {
					$document = new Ficheros();
					$em = $this->getDoctrine()->getManager();
					//propietario, nombre_fichero, nombre_real_fisico, tipo, ruta, filesize, checksum, fecha_subida, total_descargas, permiso
					$document->setPropietario($this->get('security.context')->getToken()->getUser()->getidUser());
					$document->setnombrerealfisico($document->getnombreFichero());
					$document->setTipo("fichero");
					$document->setFilesize("597");
					$document->setChecksum("258");
					$document->setfechaSubida(new \Datetime());
					$document->settotalDescargas("0");
					$document->setPermiso("si");
					//$document->upload();
					//falta modificar nombre fichero con cmd
					$em->persist($document);
					$em->flush();
					//return $this->redirect($this->generateUrl('ficheros'));
					return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
				}
				
				
			
			}
			else{
				//mostrar datos y formulario
				$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
				//ver fichero o carpeta y modificar datos.
				$ficheros=$this->getDoctrine()->getManager()->getRepository('UsuarioBundle:Ficheros')->findBy(array('idFichero'=>$fichero,'propietario' => $userid));
				//id_fichero,propietario,nombre_fichero,nombre_real_fisico,ruta,filesize,checksum,fecha_subida,total_descargas,permiso,tipo 
				$document = new Ficheros();
				$formulario = $this->createFormBuilder($document)->add('nombrefichero')->add('ruta')->getForm();
			
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
				//$ficheros=new Ficheros();
				$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
				$em->remove($ficheros);
				$em->flush();
				//falta borrar el fichero del directorio
			
				//return $this->redirect($this->generateUrl('ficheros'), 301);
				return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
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
				//procesar ruta y descargar el archivo, sumar una descarga a la BD.
				
				$em->flush();
				//borrar de la bd y del directorio
			
				//return $this->redirect($this->generateUrl('ficheros'), 301);
				return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		
		public function eventosAction(){
       
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			
			//mostrar lista de modificaciones sin mas.
			
			
			
			return $this->render('UsuarioBundle:Cuenta:eventos.html.twig');
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		public function linksAction(){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			
			//si GET, ver lista de LINKS, si link especificado, mostrar formulario para ver y modificarlo.
			
		   
			return $this->render('UsuarioBundle:Cuenta:links.html.twig');
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		public function crearlinksAction(){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			
			//si GET, mostrar formulario de crear link, si post procesarlo y redirigiar a /links.
			
		   
			return $this->render('UsuarioBundle:Cuenta:links.html.twig');
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		
}