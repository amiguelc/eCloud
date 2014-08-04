<?php
namespace eCloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Enlaces;

class LinksController extends Controller{

	public function linksAction(Request $request,$id){
	
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em=$this->getDoctrine()->getManager();
			
			//idEnlace,url,idFichero,propietario,fechaInicio,fechaExpiracion,usuarios(todos,registrados)
			$entity_enlace=new Enlaces();
			//$formulario_link = $this->createFormBuilder($entity_enlace)->add('url','text')->add('idFichero','hidden',array ('data'=> $fichero))->add('propietario','hidden',array ('data'=> $userid))->add('fechaInicio','date')->add('fechaExpiracion','date')->add('usuarios','choice', array('choices' => array('todos' => 'Todos', 'registrados' => 'Solo registrados')))->getForm();
			$formulario_link = $this->createFormBuilder($entity_enlace)->add('url','text')->add('idFichero','hidden')->add('propietario','hidden')->add('fechaInicio','date')->add('fechaExpiracion','date')->add('usuarios','choice', array('choices' => array('todos' => 'Todos', 'registrados' => 'Solo registrados')))->getForm();
			//si GET, ver lista de LINKS, si link especificado, mostrar formulario para ver y modificarlo.

			if ($request->isMethod('POST')){
				//Guardar datos
				$formulario_link->bind($request);
				if ($formulario_link->isValid()) {
					$enlace = $formulario_link->getData();
					$em->persist($enlace);
					$em->flush();
					return $this->redirect($this->generateUrl('links'), 303);
				}else{
					return $response = New Response(var_dump($formulario_link));
				}
			}
			else{
				//GET a secas-> Ver lista de links, GET con idFichero y formulario para editar.
				if($id==0){
					//Ver Lista de links.
					$enlaces=$em->getRepository('UsuarioBundle:Enlaces')->findBy(array('propietario'=>$userid));
					
					return $this->render('UsuarioBundle:Cuenta:links.html.twig', array('enlaces'=>$enlaces));
				}else{
						//Ver LINK especifico
						
						return $this->redirect($this->generateUrl('ficheros'), 303);
						//return $this->render('UsuarioBundle:Cuenta:links.html.twig');
				}
			}

			return $this->redirect($this->generateUrl('links'), 303);
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}
			
	public function borrarlinkAction($id){
   
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em=$this->getDoctrine()->getManager();
			$ficheros=$em->getRepository('UsuarioBundle:Enlaces')->findOneBy(array('idEnlace'=>$id,'propietario' => $userid));
			
			$em->remove($ficheros);
			$em->flush();
			
			return $this->redirect($this->generateUrl('links'), 303);		
		}
		else{
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}

}