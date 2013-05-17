<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Form\Frontend\UsuarioType;

class RegistroController extends Controller{

	 public function registroAction()
{
	$usuario = new Usuarios();
	$formulario = $this->createForm(new UsuarioType(), $usuario);
	return $this->render(
	'UsuarioBundle:Security:registro.html.twig',
	array('formulario' => $formulario->createView())
	);
}
	
}