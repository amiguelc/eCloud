<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Form\Frontend\UsuarioType;

class RegistroController extends Controller{

	public function registroAction(){
	 
	$peticion = $this->getRequest();
	$usuario = new Usuarios();
	$formulario = $this->createForm(new UsuarioType(), $usuario);
	
	
	
	
	if ($peticion->getMethod() == 'POST') {
		// Validar los datos enviados y guardarlos en la base de datos
		$formulario->bindRequest($peticion);
		if ($formulario->isValid()) {
		// guardar la información en la base de datos
			$encoder = $this->get('security.encoder_factory')
			->getEncoder($usuario);
			//$usuario->setSalt(md5(time()));
			$passwordCodificado = $encoder->encodePassword(
			$usuario->getPassword(),
			$usuario->getSalt()
			);
			$usuario->setIpRegistro($_SERVER['REMOTE_ADDR']);
			//$sp=new DateTimeZone("Europe\London");
			$usuario->setFechaRegistro(new \DateTime());
			$usuario->setultimoAcceso(new \DateTime());
			$usuario->setLimite("500000000");
			$usuario->setOcupado("0");
			$usuario->setLoginsftp("0");
			$usuario->setLoginweb("0");
			$usuario->setPassword($passwordCodificado);
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($usuario);
			$em->flush();
			
			$this->get('session')->setFlash('info','¡Enhorabuena! Te has registrado correctamente en eCloud');
			//$token = new UsernamePasswordToken($usuario,$usuario->getPassword(),'usuarios',$usuario->getRoles());
			//$this->container->get('security.context')->setToken($token);
			
			//crear la carpeta del usuario en el disco duro
			$var_archivos = $this->container->getParameter('var_archivos');
			mkdir($var_archivos.$usuario->getNombreUsuario());
			//falta enviar email de registro completo
			return $this->redirect($this->generateUrl('login'));
			
			
		}
	}
	
	return $this->render('UsuarioBundle:Security:registro.html.twig', array('formulario' => $formulario->createView()) );
}
	
}