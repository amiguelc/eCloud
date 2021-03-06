<?php

namespace eCloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Entity\Eventos;

class RegistroController extends Controller{

	public function registroAction(Request $request){
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			return $this->redirect($this->generateUrl('home'), 301);
		}
		else{
			 
			$usuario = new Usuarios();

			$formulario=$this->createFormBuilder($usuario, array('validation_groups' => array('registro')))
				->add('email','text')
				->add('nombre_usuario','text')
				->add('password','password')
				->add('nombre','text',array('required' => false))
				->add('apellidos','text',array('required' => false))
				->add('direccion','text',array('required' => false))
				->add('ciudad','text',array('required' => false))
				->add('pais','text',array('required' => false))
				->add('idioma', 'choice', array('choices' => array('es' => 'Español', 'en' => 'English')))
				->add('zone','timezone')
			->getForm();
			
			if ($request->getMethod() == 'POST') {
				// Validar los datos enviados y guardarlos en la base de datos
				
				$formulario->handleRequest($request);
				
				if ($formulario->isValid()) {
					// guardar la información en la base de datos
					$encoder = $this->get('security.encoder_factory')->getEncoder($usuario);
					
					//Usuario
					//$usuario->setSalt(md5(time()));
					$passwordCodificado = $encoder->encodePassword($usuario->getPassword(),$usuario->getSalt());
					$usuario->setIpRegistro($_SERVER['REMOTE_ADDR']);
					$usuario->setFechaRegistro(new \Datetime(null,new \DateTimeZone("UTC")));
					$usuario->setultimoAcceso(new \Datetime(null,new \DateTimeZone("UTC")));
					$usuario->setLimite($this->container->getParameter('default_limite'));
					$usuario->setOcupado("0");
					$usuario->setLoginsftp("0");
					$usuario->setLoginweb("0");
					$usuario->setPassword($passwordCodificado);
					$usuario->setStatus("unverified");
					$usuario->setTipo("free");
					//$usuario->setIdioma();
					$em = $this->getDoctrine()->getManager();
					
					$em->persist($usuario);
					$em->flush();
					
					
					//Crear evento de cuenta creada
					$eventos=new Eventos();

					$eventos->setIdUser($usuario->getidUser());
					$eventos->setaccion("&iexcl;Te has registrado!");
					$eventos->setTipo("0");
					$eventos->setFecha(new \Datetime(null,new \DateTimeZone("UTC")));
					$eventos->setRuta("-");
					
					$em->persist($eventos);
					$em->flush();
					
					//$token = new UsernamePasswordToken($usuario,$usuario->getPassword(),'usuarios',$usuario->getRoles());
					//$this->container->get('security.context')->setToken($token);
					
					//Crear la carpeta del usuario en el disco duro, Falta: capturar la excepcion.
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