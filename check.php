<?php
/*
Página acessible solo desde la ip 127.0.0.1. No es recomendable ejecutar este script en modo CLI ya que el entorno y variables de PHP es diferente al del entorno web.

Requisitos

    Windows o Linux. PHP 5.4.11+ y Apache2.
    Habilitar las extensiones de php php_fileinfo y php_openssl.
    Habilitar el modulo de apache rewrite para hacer las rutas amigables.
    Fichero php.ini. En el caso de wampserver vigilar que el fichero php.ini sea el que toca.
        memory_limit = 6728M <- Limite de memoria.
        upload_max_filesize 400000M <- El tamaño maximo del fichero a recibir.
        post_max_size 400000M <- El tamaño maximo de datos a recibir del metodo POST usado en los formularios de envios de datos del navegador.
        max_execution_time = 14400. <- Tiempo de ejecucion maxima de los scripts php, necesario que sea alto debido a que mientras esta recibiendo ficheros el script sigue en ejecucion.
        xdebug.max_nesting_level = 250 <- Para permitir la mas anidaciones en las funciones. En caso de no estar por defecto, incluirla.

*/

$cli=TRUE;

if (isset($_SERVER['SERVER_ADDR'])){ 
	$cli=FALSE;
	if ($_SERVER['REMOTE_ADDR']!="127.0.0.1"){echo "Página acessible solo desde la ip 127.0.0.1."; die();}
}


//DATOS DE CONFIG.YML
if (file_exists("app/config/config.yml")){
	$fichero = @fopen('app//config/config.yml', 'rb', true );
	if(!$fichero)
	{
	   $result.= 'No se puede abrir el fichero.';
	}
	$raiz="";
	$num=1;
	while (!feof($fichero))
	{
		 $linea = fgets ($fichero) ;
		// echo 'Linea '.$num.': '.$linea.'<br />';
		if ($num==73){$linea=rtrim(substr($linea,18));$raiz.="Raiz: ".$linea." Permisos de escritura: ";if (is_writable("app/config/config.yml")){$raiz.= "TRUE";}else{$raiz.="FALSE";};}
		if ($num==74){$default_limite=rtrim(substr($linea,20));}
		
		 $num++;
	}
}




$result="<style> th, td{width:150px;background-color:#EEEEEE;padding:5px;} th, td:first-child{width:35px;text-align:center;} th, td:last-child{width:auto;} </style>
<h3>Requisitos</h3>
<hr></hr>";

//Versiones de apps.

$result.=  "<table><tr><td>";if(version_compare(phpversion(), '5.4.11')==-1){$result.="error";}else{$result.="ok";}; $result.="</td><td>Versión PHP </td><td> " . phpversion()."</td><td>Necesaria version 5.4.11 o mayor</td></tr>\n";//PHP_VERSION
if ($cli==FAlSE){$result.=  "<tr><td>ok</td><td>Version Apache</td><td> ". apache_get_version()."</td><td></td></tr>\n";}
$result.= "<tr><td>ok</td><td>Sistema Operativo </td><td> ".PHP_OS."</td><td>";if (PHP_OS=="WINNT"){$result.="Windows no es case sensitive, esto afecta a como se almacenan los ficheros";} $result.="</td></tr></table>"; 

//php.ini
$result.="<h4>Fichero php.ini</h4>";

$inipath = php_ini_loaded_file();
if ($inipath) { $result.= 'Fichero php.ini cargado: ' . $inipath;} else { $result.= 'No se ha cargado ningun fichero php.ini';}
$result.=  '<br><br><table><tr><td>ok</td><td>memory_limit </td><td> ' . ini_get('memory_limit') . "</td><td>Limite de memoria utilizado por php en el servidor.</td></tr>\n";

$result.=  "<tr><td>";$result.= (return_bytes(ini_get('upload_max_filesize'))>=$default_limite) ? 'ok' : 'error'; $result.="</td><td>upload_max_filesize </td><td> " . ini_get('upload_max_filesize') . "</td><td>Tamaño maximo de un fichero a recibir. Logicamente debe ser igual o mayor al limite de espacio por cuenta (upload_max_filesize>=default_limite)</td></tr>\n";
$result.=  "<tr><td>";$result.= (return_bytes(ini_get('post_max_size'))>=$default_limite) ? 'ok' : 'error'; $result.="</td><td>post_max_size </td><td> " . ini_get('post_max_size')."</td><td>Tamaño maximo de datos que php puede recibir por el metodo POST. Igual que la anterior (post_max_size>=default_limite)</td></tr>\n";
$result.=  "<tr><td>";$result.= (ini_get('max_execution_time')>10799) ? 'ok' : 'error'; $result.="</td><td>max_execution_time </td><td> " . ini_get('max_execution_time') . " segundos </td><td>Tiempo de ejecucion maxima de los scripts php, necesario que sea alto debido a que mientras esta recibiendo ficheros el script sigue en ejecucion. Minimo 3 horas.</td></tr>\n";
//Falta chequeo de si Xdebug esta activado.
if(extension_loaded('xdebug')==TRUE){$result.=  "<tr><td>ok</td><td>xdebug.max_nesting_level </td><td> " . ini_get('xdebug.max_nesting_level') . "</td><td>Si tienes la extension de PHP Xdebug es para permitir mas anidaciones en las funciones. En caso de no estar por defecto, incluirla solo si tienes Xdebug activado.</td></tr></table><br>";}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        // El modificador 'G' está disponble desde PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

//Extensiones php
$result.= "<h4>Extensiones de php</h4>";

$result.=  "<table><tr><td>";if(extension_loaded('fileinfo')==TRUE){$result.=  "ok";}else{$result.=  "error";}; $result.="</td><td>php_fileinfo </td><td> "; if(extension_loaded('fileinfo')==TRUE){$result.=  "TRUE";}else{$result.=  "FALSE";}; $result.= "</td><td>Utilizado para obtener datos de un fichero</td></tr>\n";
$result.=  "<tr><td>";if(extension_loaded('fileinfo')==TRUE){$result.= "ok";}else{$result.=  "error";}; $result.="</td><td>php_openssl </td><td> "; if(extension_loaded('openssl')==TRUE){$result.=  "TRUE";}else{$result.=  "FALSE";}; $result.=  "</td><td>Utilizado para dar soporte a conexiones cifradas con SSL</td></tr></table>\n";


//Modulos apache
if ($cli==FAlSE){
$result.= "<h4>Modulos de Apache</h4>";

//$result.=  "Version actual: ". apache_get_version()."<br>";
//print_r(apache_get_modules());
function apache_module_exists($module_name){
    $modules = apache_get_modules();
    return ( in_array($module_name, $modules) ? true : false );
}

//var_dump(apache_module_exists('mod_headers'));


$result.=  "<table><tr><td>"; if(apache_module_exists('mod_rewrite')==TRUE){$result.=  "ok";}else{echo "error";}; $result .= "</td><td>mod_rewrite </td><td> "; if(apache_module_exists('mod_rewrite')==TRUE){$result.=  "TRUE";}else{echo "FALSE";}; $result .= "</td><td>Utilizado para hacer direcciones URL amigables</td></tr></table>";
}


//Carpeta de ecloud creada y con permiso. Acceso a la base de datos.

$result.="<h4>Carpetas y permisos</h4>";

$result .= $raiz;

//carpeta

if ($cli==FALSE){
	echo $result;
}else{
	$result=str_replace("<br>", "\n", $result);
	$result=str_replace("<h3>", "[", $result);
	$result=str_replace("</h3>", "]", $result);
	$result=str_replace("<h4>", "\n-", $result);
	$result=str_replace("</h4>", "\n\n", $result);
	$result=str_replace("<hr>", "", $result);
	$result=str_replace("</hr>", "\n", $result);
	$result=strip_tags($result);
	echo $result;
}

//Falta devolver resultado pero de comprobaciones que todo este en orden.
?>