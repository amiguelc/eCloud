<?php

namespace eCloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use eCloud\UsuarioBundle\Entity\Usuarios;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller{

	 public function loginAction(Request $request){
		$sesion = $request->getSession();
		$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR,$sesion->get(SecurityContext::AUTHENTICATION_ERROR));
				
		return $this->render('UsuarioBundle:Security:login.html.twig', array('last_username' => $sesion->get(SecurityContext::LAST_USERNAME),'error' => $error));

	}
	
	public function recoveryAction(Request $request){
		if ($request->isMethod('POST')){
			return $this->render('UsuarioBundle:Security:recovery.html.twig', array('username' => $this->get('request')->request->get('_username')));
		}
		else{
			$sesion = $request->getSession();
			return $this->render('UsuarioBundle:Security:recovery.html.twig', array('last_username' => $sesion->get(SecurityContext::LAST_USERNAME)));
		}
	}
}