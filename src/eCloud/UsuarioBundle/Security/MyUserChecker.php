<?php
namespace eCloud\UsuarioBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserChecker;

use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class MyUserChecker extends UserChecker{
	/*
	private $doctrine;

    public function __construct($doctrine){
        $this->doctrine = $doctrine;
    }
	*/
	
    /**
     * {@inheritdoc}
     */
	
    public function checkPreAuth(UserInterface $user){
		//echo var_dump($user);die();
		
		$role=$user->getRoles();		
		if($role[0]=="ROLE_ADMIN"){
			
		
		}elseif ($role[0]=="ROLE_USER"){
			if ($user->getStatus()=="locked"){
				$ex = new AuthenticationException('User account is locked!');
				throw $ex;
			}
			
			if ($user->getStatus()=="disabled"){
				$ex = new AuthenticationException('User account is disabled!');
				throw $ex;
			}
		}
        parent::checkPreAuth($user);
    }
	
    public function checkPostAuth(UserInterface $user){
		//echo var_dump($user);die();
        parent::checkPostAuth($user);
    }
	
}
?>
