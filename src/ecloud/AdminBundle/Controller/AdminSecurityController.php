<?php

namespace ecloud\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
//use Symfony\Component\Security\Core\User\User;

class AdminSecurityController extends Controller{

	 public function loginAction(){
	 
	$peticion = $this->getRequest();
	$sesion = $peticion->getSession();
	$error = $peticion->attributes->get(SecurityContext::AUTHENTICATION_ERROR,$sesion->get(SecurityContext::AUTHENTICATION_ERROR));
	
	
	//Sumar LOGIN_WEB.
	//$em = $this->getDoctrine()->getManager();
	//$usuario = $em->getRepository('UsuarioBundle:Usuarios')->find($id);
	//$usuario->setLoginWeb($usuario->getLoginWeb()+1);
	//$usuario->setLoginWeb(1);
	//$em->persist($usuario);
	//$em->flush();
	
	
	return $this->render('AdminBundle:AdminSecurity:login.html.twig', array('last_username' => $sesion->get(SecurityContext::LAST_USERNAME),'error' => $error));

	}
}