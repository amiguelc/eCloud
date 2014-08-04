<?php
namespace eCloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Entity\Eventos;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class CuentaController extends Controller{
    public function perfilAction(Request $request){
        // Obtener los datos del usuario logueado y utilizarlos para rellenar un formulario de modificacion de perfil.
		 if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em = $this->getDoctrine()->getManager();
			
			$usuario=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser' => $userid));
			$formulario_builder=$this->createFormBuilder(new Usuarios(),array('validation_groups' => array('perfil')))
				//->add('email','text',array('required' => false, 'attr' => array ('disabled' => true)))
				//->add('nombre_usuario','text',array('required' => false, 'attr' => array ('disabled' => true)))
				->add('nombre','text',array('required' => false))
				->add('apellidos','text',array('required' => false))				
				->add('direccion','text',array('required' => false))
				->add('ciudad','text',array('required' => false))
				->add('pais','text',array('required' => false))
				->add('idioma', 'choice', array('choices' => array('es' => 'Español', 'en' => 'English')))
				->add('zone','timezone',array('required' => false));
			
			if ($request->isMethod('POST')) {
				$formulario=$formulario_builder->getForm();
				$formulario->handleRequest($request);
				if ($formulario->isValid()) {
					// actualizar el perfil del usuario
					$usuario->setNombre($formulario["nombre"]->getData());
					$usuario->setApellidos($formulario["apellidos"]->getData());
					//$usuario->setEmail($formulario["email"]->getData());
					//$usuario->setNombreUsuario($formulario["nombre_usuario"]->getData());
					$usuario->setDireccion($formulario["direccion"]->getData());
					$usuario->setCiudad($formulario["ciudad"]->getData());
					$usuario->setPais($formulario["pais"]->getData());
					$usuario->setIdioma($formulario["idioma"]->getData());
					$usuario->setZone($formulario["zone"]->getData());
					
					//$usuario->setIpRegistro($this->getIpRegistro());
					//$usuario->setLimite('5555555');
					//$usuario->setPassword($this->getPassword());
					
					$em->persist($usuario);
					$em->flush();
					
					//return $this->redirect($this->generateUrl('ficheros'), 303);
				}
			}else{
				$formulario=$formulario_builder->setData($usuario)->getForm();
			}
			$usuario->setLimite(round(($usuario->getLimite()/1024/1024),2).""); //MB
			$usuario->setOcupado(round(($usuario->getOcupado()/1024/1024),2));
			$usuario->setOcupado($usuario->getOcupado().""); //MB
			$libre=($usuario->getLimite()-$usuario->getOcupado())." MB (".round((($usuario->getOcupado()/$usuario->getLimite())*100),2)."%)";
			$libre_porcentaje=round((($usuario->getOcupado()/$usuario->getLimite())*100),2);
			
			return $this->render('UsuarioBundle:Cuenta:perfil.html.twig', array('usuario'=>$usuario,'formulario' => $formulario->createView(), 'libre'=> $libre, 'libre_porcentaje'=> $libre_porcentaje));
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}
		
	public function perfiljsonAction($info){
 
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			$usuario=$this->get('security.context')->getToken()->getUser();
			
			//Enviar solo lo pedido [email,nombreusuario,limite,ocupado] sin JSON.
			switch ($info){
				case "email":
					$jsonContent=$usuario->getEmail();
					break;
				case "perfil":
					$jsonContent="{\"email\":\"".$usuario->getemail()."\", \"nombreUsuario\":\"".$usuario->getNombreUsuario()."\", \"nombre\":\"".$usuario->getNombre()."\", \"apellidos\":\"".$usuario->getApellidos()."\", \"direccion\":\"".$usuario->getdireccion()."\", \"ciudad\":\"".$usuario->getciudad()."\", \"pais\":\"".$usuario->getpais()."\", \"fechaRegistro\":\"".$usuario->getfechaRegistro()->format('d/m/Y H:i:s')."\", \"limite\":\"".$usuario->getlimite()."\", \"ocupado\":\"".$usuario->getocupado()."\", \"status\":\"".$usuario->getStatus()."\", \"tipo\":\"".$usuario->getTipo()."\"}";
					break;
				case "min":
					$jsonContent="{\"idUser\":\"".$usuario->getidUser()."\",\"email\":\"".$usuario->getemail()."\", \"nombreUsuario\":\"".$usuario->getNombreUsuario()."\"}";
					break;
				case "nombreusuario":
					$jsonContent=$usuario->getNombreUsuario();
					break;
				case "limite":
					$jsonContent=$usuario->getLimite();
					break;
				case "ocupado":
					$jsonContent=$usuario->getOcupado();
					break;
				case "libre":
					$jsonContent=$usuario->getLimite()-$usuario->getOcupado();
					break;
				case "espacio":
					$libre=$usuario->getLimite()-$usuario->getOcupado();
					$jsonContent="{\"limite\":\"".$usuario->getLimite()."\",\"ocupado\":\"".$usuario->getOcupado()."\", \"libre\":\"".$libre."\"}";
					break;
				case "status":
					$jsonContent=$usuario->getStatus();
					break;
				case "tipo":
					$jsonContent=$usuario->getTipo();
					break;
				default:
					//Envia todo en JSON...
					$usuario->setPassword("");
					$encoders = array(new XmlEncoder(), new JsonEncoder());
					$normalizers = array(new GetSetMethodNormalizer());
					$serializer = new Serializer($normalizers, $encoders);
					$jsonContent = $serializer->serialize($usuario, 'json');
					break;
			}
		
			return new Response ($jsonContent);
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}
	
	public function eventosAction(){
   
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			return $this->render('UsuarioBundle:Cuenta:eventos.html.twig');
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}
	
	public function eventosJSONAction($desde, $hasta, $start, $cantidad){
   
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			$usuario=$this->get('security.context')->getToken()->getUser();
			$userid=$usuario->getidUser();
			$em=$this->getDoctrine()->getManager();
			
			//Falta mejorar la creacion del objeto datetime para utilizar timezone Europe/Madrid u otro según el país del usuario.
			//return new Response ($desde." ".$hasta);
			if ($desde==1){
				$desde=new \Datetime(null,new \DateTimeZone("UTC"));
				$desde->modify("-3 month");
				//$desde=$desde->format("d/m/Y");
			}else{
				//Recoger datos y validarlos.
				$a=new \Datetime(null,new \DateTimeZone("UTC"));
				$desde=$a->createFromFormat("d-m-Y H:i:s", $desde." 00:00:01");
				//$desde=$desde->format("d/m/Y");
			}
			
			if ($hasta==1){
				$hasta=new \Datetime(null,new \DateTimeZone("UTC"));
				$hasta=$hasta->format("d/m/Y");
			}else{
				//Recoger datos y validarlos.
				$a=new \Datetime(null,new \DateTimeZone("UTC"));
				$hasta=$a->createFromFormat("d-m-Y H:i:s", $hasta." 23:59:59");
				//$hasta=$hasta->format("d/m/Y");
			}
			
			//Falta: Comprobar que desde es anterior a la otra.
			
			//return new Response ($start." ".$cantidad);
			
			//Enviar solo [tipo,idfichero,ruta,nombreficheroantiguo,nombreficheronuevo,fecha] sin JSON
			
			$query = $em->createQuery('SELECT e.tipo, e.idFichero, e.ruta, e.nombreFicheroAntiguo, e.nombreFicheroNuevo, e.fecha FROM UsuarioBundle:Eventos e WHERE e.idUser=?1 and e.fecha>=?2 and e.fecha<=?3 ORDER BY e.fecha DESC');
			$query->setParameter(1, $userid);
			$query->setParameter(2, $desde);
			$query->setParameter(3, $hasta);
			//$query->setParameter(4, $start);
			//$query->setParameter(5, $cantidad);
			$query->setFirstResult($start);
			$query->setMaxResults($cantidad);
			//$min=new Eventos();
			$min=$query->getResult();
			
			//Coger Timezone del usuario
			$timezone = $usuario->getZone();
			
			foreach ($min as $key => $valor) {
				$min[$key]['fecha']=$min[$key]['fecha']->setTimezone(new \DateTimeZone($timezone));
			}
			
			$encoders = array(new XmlEncoder(), new JsonEncoder());
			$normalizers = array(new GetSetMethodNormalizer());
			$serializer = new Serializer($normalizers, $encoders);
			$jsonContent = $serializer->serialize($min, 'json');
			return new Response ($jsonContent);
			
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}	

	public function conectadoAction(){
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			return new Response("true");
		}else{
			return new Response("false");
		}
	}
		
}