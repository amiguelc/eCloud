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
$result="
<h3>Requisitos</h3>
<hr></hr>";

//Versiones de apps.
$result.=  'Versión actual de PHP: ' . phpversion()."<br>";
if ($cli==FAlSE){$result.=  "Version actual de Apache: ". apache_get_version()."<br>";}
if (PHP_OS=="WINNT"){$result.= "Sistema Operativo: ".PHP_OS." <br>Recuerda que Windows no es case sensitive...<br>";}

//php.ini
$result.="<h4>Fichero php.ini</h4>";

$inipath = php_ini_loaded_file();
if ($inipath) { $result.= 'Fichero php.ini cargado: ' . $inipath;} else { $result.= 'No se ha cargado ningun fichero php.ini';}
$result.=  '<br><br>memory_limit = ' . ini_get('memory_limit') . "<br>";
$result.=  'upload_max_filesize = ' . ini_get('upload_max_filesize') . "<br>";
$result.=  'post_max_size in bytes = ' . return_bytes(ini_get('post_max_size'))."<br>";
$result.=  'max_execution_time = ' . ini_get('max_execution_time') . " segundos<br>";
$result.=  'xdebug.max_nesting_level = ' . ini_get('xdebug.max_nesting_level') . "<br>";
$result.=  'php_version = ' .PHP_VERSION."<br>";

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


$result.=  'php_fileinfo = '; if(extension_loaded('fileinfo')==TRUE){$result.=  "TRUE<br>";}else{$result.=  "FALSE<br>";}
$result.=  'php_openssl = '; if(extension_loaded('openssl')==TRUE){$result.=  "TRUE<br>\n";}else{$result.=  "FALSE<br>";}


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


$result.=  'mod_rewrite = '; if(apache_module_exists('mod_rewrite')==TRUE){$result.=  "TRUE<br>";}else{echo "FALSE<br>";}
}



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
	echo $result;
}

//Falta devolver resultado pero de comprobaciones que todo este en orden.
?>