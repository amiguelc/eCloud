<?php

namespace ecloud\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Eventos
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Eventos
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idEvento", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idEvento;

	/**
     * @var integer
     *
     * @ORM\Column(name="idUser", type="integer")
     */
    private $idUser;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="tipo", type="integer")
     */
    private $tipo;
	
    /**
     * @var string
     *
     * @ORM\Column(name="accion", type="text")
     */
    private $accion;

    /**
     * @var integer
     *
     * @ORM\Column(name="idFichero", type="integer", nullable=true)
     */
    private $idFichero;
	
	/**
     * @var string
     *
     * @ORM\Column(name="ruta", type="string", length=255)
     */
    private $ruta;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreFicheroAntiguo", type="string", length=255, nullable=true)
     */
    private $nombreFicheroAntiguo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreFicheroNuevo", type="string", length=255, nullable=true)
     */
    private $nombreFicheroNuevo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime")
     */
    private $fecha;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->idEvento;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     * @return Eventos
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
     * Set accion
     *
     * @param string $accion
     * @return Eventos
     */
    public function setAccion($accion)
    {
        $this->accion = $accion;
    
        return $this;
    }

    /**
     * Get accion
     *
     * @return string 
     */
    public function getAccion()
    {
        return $this->accion;
    }

    /**
     * Set idFichero
     *
     * @param integer $idFichero
     * @return Eventos
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
     * Set nombreFicheroAntiguo
     *
     * @param string $nombreFicheroAntiguo
     * @return Eventos
     */
    public function setNombreFicheroAntiguo($nombreFicheroAntiguo)
    {
        $this->nombreFicheroAntiguo = $nombreFicheroAntiguo;
    
        return $this;
    }

    /**
     * Get nombreFicheroAntiguo
     *
     * @return string 
     */
    public function getNombreFicheroAntiguo()
    {
        return $this->nombreFicheroAntiguo;
    }

    /**
     * Set nombreFicheroNuevo
     *
     * @param string $nombreFicheroNuevo
     * @return Eventos
     */
    public function setNombreFicheroNuevo($nombreFicheroNuevo)
    {
        $this->nombreFicheroNuevo = $nombreFicheroNuevo;
    
        return $this;
    }

    /**
     * Get nombreFicheroNuevo
     *
     * @return string 
     */
    public function getNombreFicheroNuevo()
    {
        return $this->nombreFicheroNuevo;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Eventos
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
     * Get idEvento
     *
     * @return integer 
     */
    public function getIdEvento()
    {
        return $this->idEvento;
    }

    /**
     * Set ruta
     *
     * @param string $ruta
     * @return Eventos
     */
    public function setRuta($ruta)
    {
        $this->ruta = $ruta;
    
        return $this;
    }

    /**
     * Get ruta
     *
     * @return string 
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     * @return Eventos
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}