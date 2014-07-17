<?php

namespace eCloud\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginsError
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LoginsError
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idLoginErroneo", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idLoginErroneo;
	

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
     * @ORM\Column(name="emailErroneo", type="string", length=255)
     */
    private $emailErroneo;

    /**
     * @var string
     *
     * @ORM\Column(name="passwordErroneo", type="string", length=255)
     */
    private $passwordErroneo;

    /**
     * @var string
     *
     * @ORM\Column(name="cliente", type="string", length=255)
     */
    private $cliente;


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
     * Set cliente
     *
     * @param string $cliente
     * @return Logins_error
     */
    public function setCliente($cliente)
    {
        $this->cliente = $cliente;
    
        return $this;
    }

    /**
     * Get cliente
     *
     * @return string 
     */
    public function getCliente()
    {
        return $this->cliente;
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