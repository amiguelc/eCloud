<?php

namespace eCloud\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Entity\Ficheros;

class AdminController extends Controller
{
	 public function homeAction(){
	 if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
	 
		$datos['size']=round(disk_total_space("C:")/1024/1024/1024,2);
		$datos['free_space']=round(disk_free_space("C:")/1024/1024/1024,2);
			
		//Windows	
		$wmi=new \COM('winmgmts:{impersonationLevel=impersonate}//./root/cimv2');
		foreach ($wmi->ExecQuery("SELECT TotalPhysicalMemory FROM Win32_ComputerSystem") as $cs) {$datos['memoria'] = round($cs->TotalPhysicalMemory/1024/1024,2);}
		foreach ($wmi->ExecQuery("SELECT FreePhysicalMemory FROM Win32_OperatingSystem") as $cs) {$datos['memoria_libre'] = round($cs->FreePhysicalMemory/1024,2);}
		foreach ($wmi->ExecQuery("SELECT Caption FROM Win32_OperatingSystem") as $os) {$datos['sistema']=$os->Caption;}
		foreach ($wmi->ExecQuery("SELECT Name FROM Win32_Processor") as $cpu) {$datos['cpu']=$cpu->Name;}
		foreach ($wmi->ExecQuery("SELECT LastBootUpTime FROM Win32_OperatingSystem") as $os) {$booted_str = $os->LastBootUpTime;break;}
		$booted = array(
			'year'   => substr($booted_str, 0, 4),
			'month'  => substr($booted_str, 4, 2),
			'day'    => substr($booted_str, 6, 2),
			'hour'   => substr($booted_str, 8, 2),
			'minute' => substr($booted_str, 10, 2),
			'second' => substr($booted_str, 12, 2)
		);
		$booted_ts = mktime($booted['hour'], $booted['minute'], $booted['second'], $booted['month'], $booted['day'], $booted['year']);
		function seconds_convert($uptime) {

			global $lang;
			
			// Method here heavily based on freebsd's uptime source
			$uptime += $uptime > 60 ? 30 : 0;
			$days = floor($uptime / 86400);
			$uptime %= 86400;
			$hours = floor($uptime / 3600);
			$uptime %= 3600;
			$minutes = floor($uptime / 60);
			$seconds = floor($uptime % 60);

			// Send out formatted string
			$return = array();

			if ($days > 0)
				$return[] = $days.' '." dias";
			
			if ($hours > 0)
				$return[] = $hours.' '." horas";

			if ($minutes > 0)
				$return[] = $minutes.' '." minutos";

			if ($seconds > 0)
				$return[] = $seconds. (date('m/d') == '06/03' ? ' secs' : ' '." segundos");

			return implode(', ', $return);
		}
		$datos['uptime']=seconds_convert(time() - $booted_ts) . '; iniciado el ' . date('m/d/y h:i A', $booted_ts);
		$load=array();
		foreach ($wmi->ExecQuery("SELECT LoadPercentage FROM Win32_Processor") as $cpu) {$load[] = $cpu->LoadPercentage;}
		$datos['load']=round(array_sum($load) / count($load), 2) . "%";
		//Windows
		
		
		
			
		return $this->render('AdminBundle:Admin:servidor.html.twig', array('datos'=>$datos));
		}
		else{
			return $this->redirect($this->generateUrl('admin_login'), 301);
		}
    }
	
    public function usuariosAction($id){
		if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
			$em=$this->getDoctrine()->getManager();
			$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findAll();
			
			if ($this->getRequest()->isMethod('POST')) {
			
			
			
			
			
			
			}
			else{
				if ($id!=0){
				//mostrar datos de tan solo un usuario
				$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findBy($id);
					
				}
				else{
				
				}
			
			return $this->render('AdminBundle:Admin:usuarios.html.twig', array ('usuarios'=>$usuarios));
			}
		}
		else {
			return $this->redirect($this->generateUrl('admin_login'), 301);
		}
	}

    public function modificarUsuariosAction($id){
	 if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
	 
			//$usuario=new Usuarios();
			$em=$this->getDoctrine()->getManager();
			$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findOneBy(array('idUser'=>$id));
			//id_user,email,nombre_usuario,password,nombre,apellidos,direccion,ciudad,pais,ip_registro,fecha_registro,limite,logins_ftp,login_web,ocupado,ultimo_acceso
			$formulario=$this->createFormBuilder($usuarios)->add('idUser','text')->add('email','text')->add('nombreUsuario','text')->add('password','text')->add('nombre','text')->add('apellidos','text')->add('direccion','text')
			->add('ciudad','text')->add('pais','text')->add('limite','text')->add('ocupado','text')->getForm();			
			$query=$em->createQuery('SELECT count(f.idFichero) FROM UsuarioBundle:Ficheros f WHERE f.propietario = :userid')->setParameter('userid', $id);
			$ficheros=$query->getSingleScalarResult();
	 
			return $this->render('AdminBundle:Admin:modificarusuarios.html.twig', array ('userid'=>$id,'usuarios'=>$usuarios,'formulario'=>$formulario->createView(),'ficheros'=>$ficheros));
		}
		else{
			return $this->redirect($this->generateUrl('admin_login'), 301);
		}
    }
	
	public function borrarusuariosAction($id){
		if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
			$em=$this->getDoctrine()->getManager();
			$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findOneByIdUser($id);
			$ficheros=$em->getRepository('UsuarioBundle:Ficheros')->findByPropietario($id);
			if ($this->getRequest()->isMethod('GET')) {
			//BORRAR USUARIO Y SUS CORRESPONDIENTES FICHEROS.
			
			//return $response = New Response(var_dump($ficheros));
			$em->remove($usuarios);
			foreach ($ficheros as $clave => $valor){
			$em->remove($ficheros[$clave]);
			}
			
			$em->flush();
			
			//Borrar ficheros en el filesystem
			
			function deleteDirectory($dir) {
				if (!file_exists($dir)) return true;
				if (!is_dir($dir) || is_link($dir)) return unlink($dir);
					foreach (scandir($dir) as $item) {
						if ($item == '.' || $item == '..') continue;
						if (!deleteDirectory($dir . "/" . $item)) {
							chmod($dir . "/" . $item, 0777);
							if (!deleteDirectory($dir . "/" . $item)) return false;
						};
					}
					return rmdir($dir);
				}
				
				deleteDirectory($this->container->getParameter('var_archivos')."\\".$id);
			
			}
			else{
			
			return $this->redirect($this->generateUrl('admin_usuarios'), 303);
			//return $this->render('AdminBundle:Admin:usuarios.html.twig', array ('usuarios'=>$usuarios));
			}
			return $this->redirect($this->generateUrl('admin_usuarios'), 303);
		}
		else {
			return $this->redirect($this->generateUrl('admin_login'), 301);
		}
	}
	
	
    public function statsAction(){
	 if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
			
			$em=$this->getDoctrine()->getManager();
			$usuarios=$em->getRepository('UsuarioBundle:Usuarios')->findAll();
			
			//Usuarios
			$query=$em->createQuery('SELECT COUNT(u.idUser) FROM UsuarioBundle:Usuarios u');
			$datos['registrados'] = $query->getSingleScalarResult();
			//Ficheros
			$query=$em->createQuery('SELECT COUNT(f.idFichero) FROM UsuarioBundle:Ficheros f');
			$datos['ficheros'] = $query->getSingleScalarResult();
			//Tamaño Total
			$query=$em->createQuery('SELECT SUM(f.filesize) FROM UsuarioBundle:Ficheros f');
			$datos['filesize'] = round($query->getSingleScalarResult()/1024/1024/1024,2);
			//Descargas
			$query=$em->createQuery('SELECT SUM(f.totalDescargas) FROM UsuarioBundle:Ficheros f');
			$datos['total_descargas'] = $query->getSingleScalarResult();
			//Enlaces
			$query=$em->createQuery('SELECT COUNT(e.idEnlace) FROM UsuarioBundle:Enlaces e');
			$datos['links'] = $query->getSingleScalarResult();
			
			$datos['size']=round(disk_total_space("C:")/1024/1024/1024,2);
			$datos['free_space']=round(disk_free_space("C:")/1024/1024/1024,2);
						
			return $this->render('AdminBundle:Admin:stats.html.twig', array('usuarios'=>$usuarios,'datos'=>$datos));
		}
		else{		
			return $this->redirect($this->generateUrl('admin_login'), 301);
		}
    }
}
