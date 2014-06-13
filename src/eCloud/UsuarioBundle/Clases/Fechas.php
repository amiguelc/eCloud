<?php
namespace eCloud\UsuarioBundle\Clases;

use eCloud\UsuarioBundle\Entity\Usuarios;

class Fechas {
	public function __construct(){
		
	}
	
	public static function convertFecha(\DateTime $fecha, $zona){
		$f2=$fecha->getTimestamp()+$fecha->getOffset();
		$f3=new \DateTime(null, new \DateTimeZone($zona));
		$f3->setTimestamp($f2);
		return $f3;
	}
}

?>