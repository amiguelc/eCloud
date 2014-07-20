<?php


namespace eCloud\UsuarioBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Usuarios
 *
 * @ORM\Table()
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Ese correo ya existe.",
 *     groups={"registro"}
 * )
 * @UniqueEntity(
 *     fields={"nombreUsuario"},
 *     message="Ese usuario ya existe.",
 *     groups={"registro"}
 * )
 */
class Usuarios implements UserInterface {

	function equals(\Symfony\Component\Security\Core\User\UserInterface $usuarios){
		return $this->getEmail() == $usuarios->getEmail();
	}
	
	function eraseCredentials(){
	}
	
	function getRoles(){
		return array('ROLE_USER');
	}
	
	function getUsername(){
		return $this->getEmail();
	}
	
	public function getSalt() {
		return ''; // Could also be 'mysalt'
	}
	
	/*	AdvancedUserInterface, not used...
	public function isAccountNonExpired(){
		if ($this->status=="expired"){
			return false;
		}
		
		return true;
	}

    public function isAccountNonLocked(){
		if ($this->status=="locked"){
			return false;
		}
		
		return true;
	}

    public function isCredentialsNonExpired(){
		if ($this->status=="credentialsExpired"){
			return false;
		}
		
		return true;
	}


    
    public function isEnabled(){
		if ($this->status=="disabled"){
			return false;
		}
		
		return true;
	}
	 */

    /**
     * @var integer
     *
     * @ORM\Column(name="idUser", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idUser;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, unique=true)
	 * @Assert\NotBlank()
	 * @Assert\Email(
     *     message = "El email '{{ value }}' es inválido.",
     *     checkMX = true,
	 *     groups={"registro"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreUsuario", type="string", length=255, unique=true)
	 * @Assert\NotBlank(groups={"registro"})
     */
    private $nombreUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
	 * @Assert\NotBlank()
	 * @Assert\Length(
     *      min = "6",
     *      max = "20",
     *      minMessage = "La contraseña debe tener al menos {{ limit }} carácteres",
     *      maxMessage = "La contraseña no puede tener más de {{ limit }} caracteres",
	 *		groups={"registro"}
     * )
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
	 * @Assert\Type(type="string", message="No se permite números", groups={"perfil"});
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidos", type="string", length=255, nullable=true)
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="text", nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad", type="string", length=255, nullable=true)
     */
    private $ciudad;

    /**
     * @var string
     *
     * @ORM\Column(name="pais", type="string", length=255, nullable=true)
     */
    private $pais;

    /**
     * @var string
     *
     * @ORM\Column(name="ipRegistro", type="string", length=255)
     */
    private $ipRegistro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaRegistro", type="datetime")
     */
    private $fechaRegistro;

    /**
     * @var integer
     *
     * @ORM\Column(name="limite", type="bigint")
     */
    private $limite;

    /**
     * @var integer
     *
     * @ORM\Column(name="ocupado", type="bigint")
     */
    private $ocupado;
	

    /**
     * @var integer
     *
     * @ORM\Column(name="loginsFtp", type="bigint")
     */
    private $loginsFtp;

    /**
     * @var integer
     *
     * @ORM\Column(name="loginsWeb", type="bigint")
     */
    private $loginWeb;
	
	 /**
     * @var \DateTime
     *
     * @ORM\Column(name="ultimoAcceso", type="datetime")
     */
    private $ultimoAcceso;

	 /**
     * @var string
     *
     * @ORM\Column(name="idioma", type="string", length=255)
	 * @Assert\Choice(choices = {"es", "en"}, message = "Choose ES or EN language.", groups={"registro","perfil"})
     */
    private $idioma;
	
	/**
     * @var string
     *
     * @ORM\Column(name="zone", type="string", length=255)
     */
    private $zone;

	/**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

	/**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
	 * @Assert\Choice(choices = {"free", "premium"}, message = "Invalid account")
     */
    private $tipo;

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
     * Set email
     *
     * @param string $email
     * @return Usuarios
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nombreUsuario
     *
     * @param string $nombreUsuario
     * @return Usuarios
     */
    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;
    
        return $this;
    }

    /**
     * Get nombreUsuario
     *
     * @return string 
     */
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Usuarios
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Usuarios
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set apellidos
     *
     * @param string $apellidos
     * @return Usuarios
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    
        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string 
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Usuarios
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
    
        return $this;
    }

    /**
     * Get direccion
     *
     * @return string 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     * @return Usuarios
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    
        return $this;
    }

    /**
     * Get ciudad
     *
     * @return string 
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set pais
     *
     * @param string $pais
     * @return Usuarios
     */
    public function setPais($pais)
    {
        $this->pais = $pais;
    
        return $this;
    }

    /**
     * Get pais
     *
     * @return string 
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Set ipRegistro
     *
     * @param string $ipRegistro
     * @return Usuarios
     */
    public function setIpRegistro($ipRegistro)
    {
        $this->ipRegistro = $ipRegistro;
    
        return $this;
    }

    /**
     * Get ipRegistro
     *
     * @return string 
     */
    public function getIpRegistro()
    {
        return $this->ipRegistro;
    }

    /**
     * Set fechaRegistro
     *
     * @param \DateTime $fechaRegistro
     * @return Usuarios
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;
    
        return $this;
    }

    /**
     * Get fechaRegistro
     *
     * @return \DateTime 
     */
    public function getFechaRegistro()
    {
		return $this->fechaRegistro->setTimeZone(new \DateTimeZone($this->getZone()));
        //return $this->fechaRegistro;
    }

    /**
     * Set limite
     *
     * @param integer $limite
     * @return Usuarios
     */
    public function setLimite($limite)
    {
        $this->limite = $limite;
    
        return $this;
    }

    /**
     * Get limite
     *
     * @return integer 
     */
    public function getLimite()
    {
        return $this->limite;
    }

    /**
     * Set loginsFtp
     *
     * @param integer $loginsFtp
     * @return Usuarios
     */
    public function setLoginsFtp($loginsFtp)
    {
        $this->loginsFtp = $loginsFtp;
    
        return $this;
    }

    /**
     * Get loginsFtp
     *
     * @return integer 
     */
    public function getLoginsFtp()
    {
        return $this->loginsFtp;
    }

    /**
     * Set loginWeb
     *
     * @param integer $loginWeb
     * @return Usuarios
     */
    public function setLoginWeb($loginWeb)
    {
        $this->loginWeb = $loginWeb;
    
        return $this;
    }

    /**
     * Get loginWeb
     *
     * @return integer 
     */
    public function getLoginWeb()
    {
        return $this->loginWeb;
    }

    /**
     * Set ocupado
     *
     * @param integer $ocupado
     * @return Usuarios
     */
    public function setOcupado($ocupado)
    {
        $this->ocupado = $ocupado;
    
        return $this;
    }

    /**
     * Get ocupado
     *
     * @return integer 
     */
    public function getOcupado()
    {
        return $this->ocupado;
    }

    /**
     * Set ultimoAcceso
     *
     * @param \DateTime $ultimoAcceso
     * @return Usuarios
     */
    public function setUltimoAcceso($ultimoAcceso)
    {
        $this->ultimoAcceso = $ultimoAcceso;
    
        return $this;
    }

    /**
     * Get ultimoAcceso
     *
     * @return \DateTime 
     */
    public function getUltimoAcceso()
    {
		return $this->ultimoAcceso->setTimeZone(new \DateTimeZone($this->getZone()));
        //return $this->ultimoAcceso;
    }

    /**
     * Set idioma
     *
     * @param string $idioma
     * @return Usuarios
     */
    public function setIdioma($idioma)
    {
        $this->idioma = $idioma;

        return $this;
    }

    /**
     * Get idioma
     *
     * @return string 
     */
    public function getIdioma()
    {
        return $this->idioma;
    }

    /**
     * Set zone
     *
     * @param string $zone
     * @return Usuarios
     */
    public function setZone($zone)
    {
        $this->zone = $zone;

        return $this;
    }

    /**
     * Get zone
     *
     * @return string 
     */
    public function getZone()
    {
        return $this->zone;
    }
	
    /**
     * Set status
     *
     * @param string $status
     * @return Usuarios
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
	
	/**
     * Set tipo
     *
     * @param string $tipo
     * @return Usuarios
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}
