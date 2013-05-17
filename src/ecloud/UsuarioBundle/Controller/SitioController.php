<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class SitioController extends Controller
{
	 public function EstaticasAction($pagina)
    {
        return $this->render('UsuarioBundle:Paginas:'.$pagina.'.html.twig');
    }
	
	public function HomeAction()
    {	
	//si esta logueado redirige return $this->render('UsuarioBundle:Cuenta:home.html.twig');
        return $this->render('UsuarioBundle:Paginas:home.html.twig');
    }
	
	
	
	
}