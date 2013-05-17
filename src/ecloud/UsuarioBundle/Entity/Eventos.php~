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
     * @ORM\Column(name="id_evento", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idEvento;

	/**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer")
     */
    private $idUser;
	
    /**
     * @var string
     *
     * @ORM\Column(name="accion", type="text")
     */
    private $accion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_fichero", type="integer")
     */
    private $idFichero;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_fichero_antiguo", type="string", length=255)
     */
    private $nombreFicheroAntiguo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_fichero_nuevo", type="string", length=255)
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
        return $this->id;
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
}