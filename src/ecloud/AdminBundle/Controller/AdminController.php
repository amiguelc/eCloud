<?php

namespace eCloud\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
	public function homeAction(){
	
        return $this->render('AdminBundle:Admin:usuarios.html.twig');
    }
    public function usuariosAction(){
	
        return $this->render('AdminBundle:Admin:usuarios.html.twig');
    }
	
    public function statsAction(){
	
        return $this->render('AdminBundle:Admin:stats.html.twig');
    }
}
