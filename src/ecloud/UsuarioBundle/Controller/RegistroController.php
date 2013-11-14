<?php

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Entity\Eventos;
//use eCloud\UsuarioBundle\Form\Frontend\UsuarioType;

class RegistroController extends Controller{

	public function registroAction(){
	if ($this->get('security.context')->isGranted('ROLE_USER')){
	return $this->redirect($this->generateUrl('home'), 301);
	}
	else{
	 
	$peticion = $this->getRequest();
	$usuario = new Usuarios();
	//id_user,email,nombre_usuario,password,nombre,apellidos,direccion,ciudad,pais,ip_registro,fecha_registro,limite,logins_ftp,login_web,ocupado,ultimo_acceso
	//$formulario = $this->createForm(new UsuarioType(), $usuario);
	$formulario=$this->createFormBuilder($usuario)->add('email','text')->add('nombre_usuario','text')->add('password','password')->add('nombre','text')->add('apellidos','text')->add('direccion','text')->add('ciudad','text')->add('pais','text')->getForm();
	
	
	
	if ($peticion->getMethod() == 'POST') {
		// Validar los datos enviados y guardarlos en la base de datos
		$formulario->handleRequest($peticion);
		if ($formulario->isValid()) {
		// guardar la información en la base de datos
			$encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
			
			//Usuario
			//$usuario->setSalt(md5(time()));
			$passwordCodificado = $encoder->encodePassword($usuario->getPassword(),$usuario->getSalt());
			$usuario->setIpRegistro($_SERVER['REMOTE_ADDR']);
			//$sp=new DateTimeZone("Europe\London");
			$usuario->setFechaRegistro(new \DateTime());
			$usuario->setultimoAcceso(new \DateTime());
			$usuario->setLimite($this->container->getParameter('default_limite'));
			$usuario->setOcupado("0");
			$usuario->setLoginsftp("0");
			$usuario->setLoginweb("0");
			$usuario->setPassword($passwordCodificado);
			$em = $this->getDoctrine()->getManager();
			
			$em->persist($usuario);
			$em->flush();
			
			
			//Crear evento de cuenta creada
			$eventos=new Eventos();
			//id_evento,id_user,accion,id_fichero,nombre_fichero_antiguo,nombre_fichero_nuevo,fecha 
			$eventos->setIdUser($usuario->getidUser());
			$eventos->setaccion("&iexcl;Te has registrado!");
			$eventos->setFecha(new \Datetime());
			$eventos->setRuta("-");
			
			//$em2 = $this->getDoctrine()->getManager();
			$em->persist($eventos);
			$em->flush();
			
			//$token = new UsernamePasswordToken($usuario,$usuario->getPassword(),'usuarios',$usuario->getRoles());
			//$this->container->get('security.context')->setToken($token);
			
			//crear la carpeta del usuario en el disco duro
			$var_archivos = $this->container->getParameter('var_archivos');
			mkdir($var_archivos.$usuario->getIdUser());
			
			
			//falta enviar email de registro completo
			return $this->redirect($this->generateUrl('login'));
			
			
		}
	}
	
	return $this->render('UsuarioBundle:Security:registro.html.twig', array('formulario' => $formulario->createView()) );
}
}
}