<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use eCloud\UsuarioBundle\Form\Frontend\UsuarioType;

class CuentaController extends Controller{
    public function perfilAction(){
        // Obtener los datos del usuario logueado y utilizarlos para
		// rellenar un formulario de registro.
		//
		// Si la petición es GET, mostrar el formulario
		// Si la petición es POST, actualizar la información del usuario con
		// los nuevos datos obtenidos del formulario
		
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
		
		public function ficherosAction(){
       
		return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
		}
		
		public function subirAction(){
       
		return $this->render('UsuarioBundle:Cuenta:subir.html.twig');
		}
		
		public function eventosAction(){
       
		return $this->render('UsuarioBundle:Cuenta:eventos.html.twig');
		}
		
		
}