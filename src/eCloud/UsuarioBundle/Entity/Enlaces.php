<?php

namespace eCloud\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enlaces
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Enlaces
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idEnlace", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idEnlace;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="idFichero", type="bigint")
     */
    private $idFichero;

    /**
     * @var integer
     *
     * @ORM\Column(name="propietario", type="bigint")
     */
    private $propietario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicio", type="datetime")
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaExpiracion", type="datetime")
     */
    private $fechaExpiracion;

    /**
     * @var string
     *
     * @ORM\Column(name="usuarios", type="string", length=255)
     */
    private $usuarios;


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
     * Set url
     *
     * @param string $url
     * @return Enlaces
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set idFichero
     *
     * @param integer $idFichero
     * @return Enlaces
     */
    public function setIdFichero($idFichero)
    {
        $this->idFichero = $idFichero;
    
        return $this;
    }

    /**
     * Get idFichero
     *
     * @return integer 
     */
    public function getIdFichero()
    {
        return $this->idFichero;
    }

    /**
     * Set propietario
     *
     * @param integer $propietario
     * @return Enlaces
     */
    public function setPropietario($propietario)
    {
        $this->propietario = $propietario;
    
        return $this;
    }

    /**
     * Get propietario
     *
     * @return integer 
     */
    public function getPropietario()
    {
        return $this->propietario;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return Enlaces
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    
        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaExpiracion
     *
     * @param \DateTime $fechaExpiracion
     * @return Enlaces
     */
    public function setFechaExpiracion($fechaExpiracion)
    {
        $this->fechaExpiracion = $fechaExpiracion;
    
        return $this;
    }

    /**
     * Get fechaExpiracion
     *
     * @return \DateTime 
     */
    public function getFechaExpiracion()
    {
        return $this->fechaExpiracion;
    }

    /**
     * Set usuarios
     *
     * @param string $usuarios
     * @return Enlaces
     */
    public function setUsuarios($usuarios)
    {
        $this->usuarios = $usuarios;
    
        return $this;
    }

    /**
     * Get usuarios
     *
     * @return string 
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * Get idEnlace
     *
     * @return integer 
     */
    public function getIdEnlace()
    {
        return $this->idEnlace;
    }
}