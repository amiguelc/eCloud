<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SitioController extends Controller{
	
	public function HomeAction(){
		//si esta logueado redirige return $this->render('UsuarioBundle:Cuenta:home.html.twig');
		
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
		// el usuario tiene el role 'ROLE_USUARIO'
		return $this->render('UsuarioBundle:Cuenta:home.html.twig');
		}
		else{
		// el usuario anonymous
		return $this->render('UsuarioBundle:Paginas:home.html.twig');
		}
    }
	
	public function ayudaAction(){
        return $this->render('UsuarioBundle:Paginas:ayuda.html.twig');
    }
	public function contactoAction(){
        return $this->render('UsuarioBundle:Paginas:contacto.html.twig');
    }
	public function privacidadAction(){
        return $this->render('UsuarioBundle:Paginas:privacidad.html.twig');
    }
	public function sobrenosotrosAction(){
        return $this->render('UsuarioBundle:Paginas:sobre-nosotros.html.twig');
    }
}