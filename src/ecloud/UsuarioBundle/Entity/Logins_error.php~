<?php

namespace ecloud\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logins_error
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Logins_error
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_loginerroneo", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idLoginErroneo;
	
	 /**
     * @var string
     *
     * @ORM\Column(name="id_user", type="integer")
     */
    private $idUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="email_erroneo", type="string", length=255)
     */
    private $emailErroneo;

    /**
     * @var string
     *
     * @ORM\Column(name="password_erroneo", type="string", length=255)
     */
    private $passwordErroneo;

    /**
     * @var string
     *
     * @ORM\Column(name="metodo", type="string", length=255)
     */
    private $metodo;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     * @return Logins_error
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    
        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer 
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Logins_error
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Logins_error
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set emailErroneo
     *
     * @param string $emailErroneo
     * @return Logins_error
     */
    public function setEmailErroneo($emailErroneo)
    {
        $this->emailErroneo = $emailErroneo;
    
        return $this;
    }

    /**
     * Get emailErroneo
     *
     * @return string 
     */
    public function getEmailErroneo()
    {
        return $this->emailErroneo;
    }

    /**
     * Set passwordErroneo
     *
     * @param string $passwordErroneo
     * @return Logins_error
     */
    public function setPasswordErroneo($passwordErroneo)
    {
        $this->passwordErroneo = $passwordErroneo;
    
        return $this;
    }

    /**
     * Get passwordErroneo
     *
     * @return string 
     */
    public function getPasswordErroneo()
    {
        return $this->passwordErroneo;
    }

    /**
     * Set metodo
     *
     * @param string $metodo
     * @return Logins_error
     */
    public function setMetodo($metodo)
    {
        $this->metodo = $metodo;
    
        return $this;
    }

    /**
     * Get metodo
     *
     * @return string 
     */
    public function getMetodo()
    {
        return $this->metodo;
    }

    /**
     * Get idLoginErroneo
     *
     * @return integer 
     */
    public function getIdLoginErroneo()
    {
        return $this->idLoginErroneo;
    }
}