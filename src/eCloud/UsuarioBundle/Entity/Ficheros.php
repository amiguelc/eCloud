<?php

namespace eCloud\UsuarioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Ficheros
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Ficheros
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idFichero", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idFichero;

    /**
     * @var integer
     *
     * @ORM\Column(name="propietario", type="bigint")
     */
    private $propietario;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreFichero", type="string", length=255)
     */
    private $nombreFichero;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreRealFisico", type="string", length=255)
     */
    private $nombreRealFisico;

	
    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;
	
    /**
     * @var string
     *
     * @ORM\Column(name="ruta", type="string", length=255)
     */
    private $ruta;

    /**
     * @var integer
     *
     * @ORM\Column(name="filesize", type="bigint")
     */
    private $filesize;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=255)
     */
    private $checksum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaSubida", type="datetime")
     */
    private $fechaSubida;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modificacion", type="datetime")
     */
    private $modificacion;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="totalDescargas", type="bigint")
     */
    private $totalDescargas;

    /**
     * @var string
     *
     * @ORM\Column(name="permiso", type="string", length=2)
     */
    private $permiso;

	 /**
     * @var string
     *
     * @ORM\Column(name="mime", type="string", length=255)
     */
    private $mime;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->idFichero;
    }

    /**
     * Set idFichero
     *
     * @param integer $idFichero
     * @return Ficheros
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
     * @return Ficheros
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
     * Set nombreFichero
     *
     * @param string $nombreFichero
     * @return Ficheros
     */
    public function setNombreFichero($nombreFichero)
    {
        $this->nombreFichero = $nombreFichero;
    
        return $this;
    }

    /**
     * Get nombreFichero
     *
     * @return string 
     */
    public function getNombreFichero()
    {
        return $this->nombreFichero;
    }

    /**
     * Set nombreRealFisico
     *
     * @param string $nombreRealFisico
     * @return Ficheros
     */
    public function setNombreRealFisico($nombreRealFisico)
    {
        $this->nombreRealFisico = $nombreRealFisico;
    
        return $this;
    }

    /**
     * Get nombreRealFisico
     *
     * @return string 
     */
    public function getNombreRealFisico()
    {
        return $this->nombreRealFisico;
    }

    /**
     * Set ruta
     *
     * @param string $ruta
     * @return Ficheros
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
     * Set filesize
     *
     * @param integer $filesize
     * @return Ficheros
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;
    
        return $this;
    }

    /**
     * Get filesize
     *
     * @return integer 
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     * @return Ficheros
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;
    
        return $this;
    }

    /**
     * Get checksum
     *
     * @return string 
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set fechaSubida
     *
     * @param \DateTime $fechaSubida
     * @return Ficheros
     */
    public function setFechaSubida($fechaSubida)
    {
        $this->fechaSubida = $fechaSubida;
    
        return $this;
    }

    /**
     * Get fechaSubida
     *
     * @return \DateTime 
     */
    public function getFechaSubida()
    {
        return $this->fechaSubida;
    }


    /**
     * Set modificacion
     *
     * @param \DateTime $modificacion
     * @return Ficheros
     */
    public function setModificacion($modificacion)
    {
        $this->modificacion = $modificacion;
    
        return $this;
    }

    /**
     * Get modificacion
     *
     * @return \DateTime 
     */
    public function getModificacion()
    {
        return $this->modificacion;
    }
	public function setFechasCorrectas($zone){
		if ($zone==null){$zone="UTC";}
		$this->fechaSubida->setTimeZone(new \DateTimeZone($zone));
		$this->modificacion->setTimeZone(new \DateTimeZone($zone));
		//$this->modificacion=\DateTime::createFromFormat("d/m/Y H:i" , $this->modificacion->format("d/m/Y H:i") , new \DateTimeZone($zone));
		//$this->fechaSubida=\DateTime::createFromFormat("d/m/Y H:i" , $this->fechaSubida->format("d/m/Y H:i"), new \DateTimeZone($zone));
		//$this->fechaSubida=new \DateTime
		return 1;
	}
	
    /**
     * Set totalDescargas
     *
     * @param integer $totalDescargas
     * @return Ficheros
     */
    public function setTotalDescargas($totalDescargas)
    {
        $this->totalDescargas = $totalDescargas;
    
        return $this;
    }

    /**
     * Get totalDescargas
     *
     * @return integer 
     */
    public function getTotalDescargas()
    {
        return $this->totalDescargas;
    }

    /**
     * Set permiso
     *
     * @param string $permiso
     * @return Ficheros
     */
    public function setPermiso($permiso)
    {
        $this->permiso = $permiso;
    
        return $this;
    }

    /**
     * Get permiso
     *
     * @return string 
     */
    public function getPermiso()
    {
        return $this->permiso;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return Ficheros
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
	
		/**
		* 
		*/
		private $file;
		/**
		* Sets file.
		*
		* @param UploadedFile $file
		*/
		public function setFile(UploadedFile $file = null){
		$this->file = $file;
		}
		/**
		* Get file.
		*
		* @return UploadedFile
		*/
		public function getFile(){
		return $this->file;
		}
		
		public function upload($var_archivos){
			// the file property can be empty if the field is not required
			if (null === $this->getFile()) {
			return;
			}
						
			// use the original file name here but you should
			// sanitize it at least to avoid any security issues
			// move takes the target directory and then the
			// target filename to move to
			//$this->getFile()->move($this->getUploadRootDir(),$this->getFile()->getClientOriginalName());

			$this->setMime($this->getFile()->getMimeType());
			$nombre=$this->getFile()->getClientOriginalName();
			
			
			//AQUI FALTA COMPROBAR ANTES SI YA EXISTE EL FICHERO CON EL MISMO NOMBRE Y COMPROBACIONES EN EL NOMBRE DEL FICHERO
			
			
			$this->getFile()->move($var_archivos.print_r($this->getPropietario(), true).$this->getRuta(),$nombre);
			// set the path property to the filename where you've saved the file
			$this->path = $nombre;
			
			//Coger nombre, tama�o en bytes y checksum.
			$fichero_subido=$var_archivos.print_r($this->getPropietario(), true).$this->getRuta()."/".$nombre;
			$this->setChecksum(md5_file($fichero_subido));
			$this->setNombreFichero($nombre);
			$this->setnombrerealfisico($nombre);
			$this->setFilesize(filesize($fichero_subido));
			
			
			// clean up the file property as you won't need it anymore
			$this->file = null;
		}
		
		public function remove($var_archivos){
		
		$fichero_subido=$var_archivos.print_r($this->getPropietario(), true).$this->getRuta()."/".$this->getNombreFichero();
		if (file_exists($fichero_subido)){
			unlink($fichero_subido);
		}
	}	

    /**
     * Set mime
     *
     * @param string $mime
     * @return Ficheros
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
    
        return $this;
    }

    /**
     * Get mime
     *
     * @return string 
     */
    public function getMime()
    {
        return $this->mime;
    }
}