<?php

namespace eCloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use eCloud\UsuarioBundle\Entity\Usuarios;

class SecurityController extends Controller{

	 public function loginAction(){
	 
		$peticion = $this->getRequest();
		$sesion = $peticion->getSession();
		$error = $peticion->attributes->get(SecurityContext::AUTHENTICATION_ERROR,$sesion->get(SecurityContext::AUTHENTICATION_ERROR));
				
		return $this->render('UsuarioBundle:Security:login.html.twig', array('last_username' => $sesion->get(SecurityContext::LAST_USERNAME),'error' => $error));

	}
	
	public function recoveryAction(){
		if ($this->getRequest()->isMethod('POST')){
			return $this->render('UsuarioBundle:Security:recovery.html.twig', array('username' => $this->get('request')->request->get('_username')));
		}
		else{
			$peticion = $this->getRequest();
			$sesion = $peticion->getSession();
			return $this->render('UsuarioBundle:Security:recovery.html.twig', array('last_username' => $sesion->get(SecurityContext::LAST_USERNAME)));
		}
	}
}