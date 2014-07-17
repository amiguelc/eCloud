<?php
namespace eCloud\UsuarioBundle\Listener;

use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use eCloud\UsuarioBundle\Entity\Logins;
use eCloud\UsuarioBundle\Entity\LoginsError;

class AuthenticationListener{

	private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }
	
	public function onAuthenticationFailure( AuthenticationFailureEvent $event ){
		$em=$this->doctrine->getManager();
		$usuario = $event->getAuthenticationToken()->getUser();
		
		$query = $em->createQuery('SELECT COUNT(u) FROM UsuarioBundle:Usuarios u WHERE u.email=?1');
		$query->setParameter(1, $_POST['_username']);
		$result=$query->getSingleScalarResult();
		
		//Si existe cuenta
		if($result==1){
			$query = $em->createQuery('SELECT COUNT(le.emailErroneo) FROM UsuarioBundle:LoginsError le WHERE le.emailErroneo=?1');
			$query->setParameter(1, $_POST['_username']);
			$result=$query->getSingleScalarResult();
			if ($result>4){
				$query = $em->createQuery("UPDATE UsuarioBundle:Usuarios u SET u.status = '2' WHERE u.email=?1");
				$query->setParameter(1, $_POST['_username']);
				$result=$query->getSingleScalarResult();
			}else{
				$logins_error=new LoginsError();
				$logins_error->setFecha(new \Datetime(null,new \DateTimeZone("UTC")));
				$logins_error->setIp($_SERVER['REMOTE_ADDR']);
				$logins_error->setEmailErroneo($_POST['_username']);
				$logins_error->setPasswordErroneo($_POST['_password']);
				$logins_error->setCliente($_SERVER['HTTP_USER_AGENT']);		
				
				$em->persist($logins_error);
				$em->flush();
			}
		}
        
    }

	public function onAuthenticationSuccess( InteractiveLoginEvent $event ){
		$em=$this->doctrine->getManager();
		
		$usuario = $event->getAuthenticationToken()->getUser();
		$usuario->setLoginWeb($usuario->getLoginWeb()+1);
		$usuario->setUltimoAcceso(new \Datetime(null,new \DateTimeZone("UTC")));
		
		$logins=new Logins();
		$logins->setIdUser($usuario->getIdUser());
		$logins->setFecha(new \Datetime(null,new \DateTimeZone("UTC")));
		$logins->setIp($_SERVER['REMOTE_ADDR']);
		$logins->setCliente($_SERVER['HTTP_USER_AGENT']);		
		
		//Borrar logins erroneos		
		$query = $em->createQuery('Delete FROM UsuarioBundle:LoginsError le WHERE le.emailErroneo=?1');
		$query->setParameter(1, $_POST['_username']);
		$result=$query->getResult();
		
		
		$em->persist($logins);
		$em->persist($usuario);
		$em->flush();
	}
	
	
}
?>