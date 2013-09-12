<?php
//devolver en format html los eventos del usuario, asi que esto debe ser un controlador llamado ajax y cada accion sacara algo, eventos, lista de archivos, etc

namespace ecloud\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use eCloud\UsuarioBundle\Entity\Usuarios;
use eCloud\UsuarioBundle\Entity\Ficheros;
use eCloud\UsuarioBundle\Entity\Eventos;
use eCloud\UsuarioBundle\Entity\Enlaces;




class ajaxController extends Controller{
    public function cargarJsonAction(){}
	
	
}


?>