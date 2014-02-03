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
	
	/*
	//Sumar LOGIN_WEB.
		$em = $this->getDoctrine()->getManager();
	$usuario = $em->getRepository('UsuarioBundle:Usuarios')->find($id);
	//$usuario->setLoginWeb($usuario->getLoginWeb()+1);
	$usuario->setLoginWeb(1);
	$em->persist($usuario);
	$em->flush();
	*/
	
	return $this->render('UsuarioBundle:Security:login.html.twig', array('last_username' => $sesion->get(SecurityContext::LAST_USERNAME),'error' => $error));

	}
}