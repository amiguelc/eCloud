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
		
		//Nota: Proteger controlador
		 if ($this->get('security.context')->isGranted('ROLE_USER')){
			
			
			$usuario = $this->get('security.context')->getToken()->getUser();
			$formulario = $this->createForm(new UsuarioType(), $usuario);
			$peticion = $this->getRequest();
			if ($peticion->getMethod() == 'POST') {
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
			$peticion = $this->getRequest();
			
			if ($peticion->getMethod() == 'POST') {
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
			
			/*
			/*
			//mostrar formulario de subir fichero
			//$peticion = $this->getRequest();
			//$request= Request::createFromGlobals();
				if($this->getRequest()->isMethod('POST')) {
				//guardar fichero y tal
				$form->bind($this->getRequest());
				//return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
					if ($form->isValid()) {
						//$var_archivos = $this->container->getParameter('var_archivos');
						//$fil="0.txt";
						//$formulario['attachment']->getData()->move($var_archivos, $fil);
						return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
					}
				}else{
				
				//mostrar formulario
				$ficheros = new Ficheros();
				$formulario = $this->createForm(new SubirFicheroType());
				return $this->render('UsuarioBundle:Cuenta:subir.html.twig', array('formulario' => $formulario->createView()));
				
				}
				*/
				$document = new Ficheros();
				$formulario = $this->createFormBuilder($document)->add('nombrefichero')->add('file','file')->getForm();
				if ($this->getRequest()->isMethod('POST')) {
				$formulario->bind($this->getRequest());
					if ($formulario->isValid()) {
						$em = $this->getDoctrine()->getManager();
						//propietario, nombre_fichero, nombre_real_fisico, tipo, ruta, filesize, checksum, fecha_subida, total_descargas, permiso
						$document->setPropietario($this->get('security.context')->getToken()->getUser()->getidUser());
						$document->setnombreFichero("eeeeeeee");
						$document->setnombrerealfisico("eeeeee");
						$document->setTipo("fichero");
						$document->setRuta("/");
						$document->setFilesize("596");
						$document->setChecksum("258");
						$document->setfechaSubida(new \Datetime());
						$document->settotalDescargas("0");
						$document->setPermiso("si");
						$document->upload();
						
						$em->persist($document);
						$em->flush();
						//return $this->redirect($this->generateUrl('ficheros'));
						return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
					}
				}
				return $this->render('UsuarioBundle:Cuenta:subir.html.twig', array('formulario' => $formulario->createView()));
				
				
				
			}
			else{
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
		
		public function linksAction(){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
		   
			return $this->render('UsuarioBundle:Cuenta:links.html.twig');
			}
			else{
				return $this->redirect($this->generateUrl('login'), 301);
			}
		}
		
		
}