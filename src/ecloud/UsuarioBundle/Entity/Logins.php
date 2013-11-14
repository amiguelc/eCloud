<?php

namespace ecloud\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logins
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Logins
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idLogin", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idLogin;

    /**
     * @var integer
     *
     * @ORM\Column(name="idUser", type="bigint")
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
     * @ORM\Column(name="metodo", type="string", length=3)
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
     * @return Logins
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
     * @return Logins
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
     * @return Logins
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
     * Set metodo
     *
     * @param string $metodo
     * @return Logins
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
     * Get idLogin
     *
     * @return integer 
     */
    public function getIdLogin()
    {
        return $this->idLogin;
    }
}