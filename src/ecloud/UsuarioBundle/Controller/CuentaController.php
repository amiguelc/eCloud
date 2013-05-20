<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Entity\Ficheros;
use eCloud\UsuarioBundle\Form\Frontend\UsuarioType;

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
		
		public function ficherosAction(){
		
		//leer los ficheros de la base de datos y pasarselos a twig con ajax, aqui se complica mucho la cosa
		//quizas con codigo javascript se pueda subir algun fichero
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
			
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$peticion = $this->getRequest();
			
			if ($peticion->getMethod() == 'POST') {
				//guardar fichero y tal
			}else{
				//coger ficheros de ese usuario y pasarlos a la plantilla twig
				$ficheros=$this->getDoctrine()->getManager()->getRepository('UsuarioBundle:Ficheros')->findByPropietario($userid);
			}
			
			
			
			return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig',array('ficheros' => $ficheros));
			
			}
			else{
					return $this->redirect($this->generateUrl('login'), 301);
			}
		
		}
		
		public function subirAction(){
		
			if ($this->get('security.context')->isGranted('ROLE_USER')){
				return $this->render('UsuarioBundle:Cuenta:subir.html.twig');
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