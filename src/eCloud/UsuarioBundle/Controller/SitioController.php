<?php

namespace eCloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SitioController extends Controller{
	
	public function HomeAction(){
		
		if ($this->get('security.context')->isGranted('ROLE_USER')) {
			return $this->redirect($this->generateUrl('ficheros'), 303);
		}
		else{
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