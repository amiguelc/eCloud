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
        // check anything
		//$em=$this->doctrine->getManager();
		/*
		$em=$this->get('doctrine.orm.entity_manager');
		*/
		
		if ($user->getStatus()=="2"){
			$ex = new AuthenticationException('User account is locked!');
			throw $ex;
		}
		
		if ($user->getStatus()=="4"){
			$ex = new AuthenticationException('User account is disabled!');
			throw $ex;
		}
		
        parent::checkPreAuth($user);
    }
	
    public function checkPostAuth(UserInterface $user){
        // check anything

        parent::checkPostAuth($user);
    }
	
}
?>
