<?php
namespace eCloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DescargasController extends Controller{

	//Descarga INTERNA
	public function descargaInternaAction($fichero){
   
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em=$this->getDoctrine()->getManager();
			$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$fichero,'propietario' => $userid));
			if (!$ficheros) {throw $this->createNotFoundException('No existe el fichero '.$fichero);}
			
			//procesar ruta y descargar el archivo, sumar una descarga a la BD.
			$ruta_local=$this->container->getParameter('var_archivos').$userid.print_r($ficheros->getRuta(), true)."/".print_r($ficheros->getNombreFichero(), true);
			$ruta_local=str_replace("//", "/", $ruta_local);
			$ficheros->setTotalDescargas($ficheros->getTotalDescargas()+1);
			$mime=$ficheros->getMime();
			//sumar descarga a la bd
			$em->flush();
			
			//servir archivo
			$headers = array('Content-Type' => $ficheros->getMime(),'Content-Disposition' => 'attachment; filename="'.$ficheros->getNombreFichero().'"');  
			
			return new Response(file_get_contents($ruta_local), 200, $headers);
		}
		else{		
			//falta mostrar descarga a usuarios publicos, los links apuntaran aqui.
			return $this->redirect($this->generateUrl('login'), 301);
		}
	}
	
	//Descarga EXTERNA ecloud/!idlink
	public function descargaExternaAction(Request $request,$descarga){
   
		if ($this->get('security.context')->isGranted('ROLE_USER')){
			//$userid=$this->get('security.context')->getToken()->getUser()->getidUser();
			$em=$this->getDoctrine()->getManager();
			$enlace=$em->getRepository('UsuarioBundle:Enlaces')->findOneBy(array('idEnlace'=>$descarga));
			if (!$enlace) {throw $this->createNotFoundException('No existe ese fichero');}
			$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$enlace->getIdFichero()));
			if (!$ficheros) {throw $this->createNotFoundException('No existe ese fichero');}
			//procesar ruta y descargar el archivo, sumar una descarga a la BD.
			$ruta_local=$this->container->getParameter('var_archivos').$ficheros->getPropietario().print_r($ficheros->getRuta(), true)."/".print_r($ficheros->getNombreFichero(), true);
			$ruta_local=str_replace("//", "/", $ruta_local);
			$ficheros->setTotalDescargas($ficheros->getTotalDescargas()+1);
			$mime=$ficheros->getMime();
			//sumar descarga a la bd
			$em->flush();
			
			//servir archivo
			$headers = array('Content-Type' => $ficheros->getMime(),'Content-Disposition' => 'attachment; filename="'.$ficheros->getNombreFichero().'"');  
			
			return new Response(file_get_contents($ruta_local), 200, $headers);

			//return $this->redirect($this->generateUrl('ficheros'), 303);
			//return $this->render('UsuarioBundle:Cuenta:ficheros.html.twig');
		}
		else{
		
			//FALTA MOSTRAR FICHERO Y ESPERA PARA DETERMINADA IP. ¿Hacer una tabla para la cola de espera? Mejor una session con timer.
			$em=$this->getDoctrine()->getManager();
			$enlace=$em->getRepository('UsuarioBundle:Enlaces')->findOneBy(array('idEnlace'=>$descarga));
			if (!$enlace) {throw $this->createNotFoundException('No existe ese fichero');}
			$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findOneBy(array('idFichero'=>$enlace->getIdFichero()));
			if (!$ficheros) {throw $this->createNotFoundException('No existe ese fichero');}
			
			if($enlace->getUsuarios()=='todos'){
				//procesar enlace y servir al usuario.
				
				if ($request->isMethod('POST')){
					//procesar ruta y descargar el archivo, sumar una descarga a la BD.
					$ruta_local=$this->container->getParameter('var_archivos').$ficheros->getPropietario().print_r($ficheros->getRuta(), true)."/".print_r($ficheros->getNombreFichero(), true);
					$ruta_local=str_replace("//", "/", $ruta_local);
					$ficheros->setTotalDescargas($ficheros->getTotalDescargas()+1);
					$mime=$ficheros->getMime();
					//sumar descarga a la bd
					$em->flush();
					
					//servir archivo
					$headers = array('Content-Type' => $ficheros->getMime(),'Content-Disposition' => 'attachment; filename="'.$ficheros->getNombreFichero().'"');  
					
					return new Response(file_get_contents($ruta_local), 200, $headers);
				}
				
				$ficheros->setFilesize(round($ficheros->getFilesize()/1024/1024,2));
				
				return $this->render('UsuarioBundle:Cuenta:descarga.html.twig', array('fichero'=>$ficheros,'error'=>''));
			}
			else{
				//mostrar error de solo para usuario registrados.			
				return $this->render('UsuarioBundle:Cuenta:descarga.html.twig', array('fichero'=>$ficheros,'error'=>'Solo para usuarios registrados'));
			}		
		
			//falta mostrar descarga a usuarios publicos, los links apuntaran aqui.
			
			//return $this->redirect($this->generateUrl('login'), 301);
		}
	}


}