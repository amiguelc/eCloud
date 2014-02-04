<?php
/*
Página acessible solo desde la ip 127.0.0.1

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

if ($_SERVER['REMOTE_ADDR']!="127.0.0.1"){echo "Página acessible solo desde la ip 127.0.0.1."; die();}

if (!isset($_GET['paso'])){$_GET['paso']=1;}


echo "<h2>Instalador de eCloud</h2>";
echo "<hr></hr>";

//Primer paso
if ($_GET['paso']==1){
	echo "<h3>Primer paso: Cumplir los requisitos</h3>";
	/*
	$salida = shell_exec('php check.php');
	echo "<pre>$salida</pre>";
	*/
	include "check.php";
	
	echo "<br><input type='button' value='Siguiente' onclick=\"location.search='?paso=2'\">";
}

//Segundo paso
if ($_GET['paso']==2){
	echo "<h3>Segundo paso: Configurar ficheros</h3>";
	//Falta copiar este fichero a parameters.yml y crear un token cada vez que se abra el fichero al guardarlo.
	echo "<h4> Fichero app/config/parameters.yml para configurar la base de datos</h4>";
	$parameters=file_get_contents("app/config/parameters.yml");
	echo "<textarea cols='100' rows='15'>".$parameters."</textarea>";
	
	echo "<h4> Fichero app/config/config.yml para configurar las variables var_archivos y limite-default</h4>";
	$config=file_get_contents("app/config/config.yml");
	echo "<textarea cols='100' rows='15'>".$config."</textarea>";
	
	echo "<h4> Fichero app/config/security.yml para configurar la contraseña de administrador por defecto admin:admin1</h4>";
	$security=file_get_contents("app/config/security.yml");
	echo "<textarea cols='100' rows='15'>".$security."</textarea>";
	
	echo "<br><input type='button' value='Siguiente' onclick=\"location.search='?paso=3'\">";
}

//Tercer paso
if ($_GET['paso']==3){
	echo "<h3>Tercer paso: Actualizar dependencias del proyecto eCloud</h3>";
	
	$content = file_get_contents('https://getcomposer.org/installer');
	if ($content !== false) {
		// do something with the content
		file_put_contents("installer", $content);
		$salida = shell_exec('php installer');
		echo "<pre>$salida</pre>";
		if (file_exists("composer.phar")) {
			$salida = shell_exec('php composer.phar install');
			echo "<pre>$salida</pre>";
		} else {
			echo "No se encuentra el archivo composer.phar";
		}
	} else {
		// an error happened
		echo "Error al descargar el instalador de composer.";
	}
			
	
	
	//echo "curl -s https://getcomposer.org/installer | php php composer.phar install";

	echo "<h3>Y ejecutar php app/check.php, para ver si el servidor cumple los requisitos del framework Symfony2.</h3>";
	//Entre ellos mirar si cache y logs tienen permisos de escritura.
	$salida = shell_exec('php app/check.php');
	echo "<pre>$salida</pre>";
	
	echo "<br><input type='button' value='Siguiente' onclick=\"location.search='?paso=4'\">";
}

//Cuarto paso
if ($_GET['paso']==4){
	echo "<h3>Cuarto paso: Crear bases de datos, y copiar imagenes y ficheros css y js</h3>";
	echo "php app/console doctrine:schema:create <br>";
	echo "php app/console assets:install web <br>";
	echo "php app/console assetic:dump --env=prod –no-debug";
	echo "<br><input type='button' value='Siguiente' onclick=\"location.search='?paso=5'\">";
}

//Ultimo paso
if ($_GET['paso']==5){
	echo "<h3>Ultimo paso: Configurar el Virtualhost de Apache</h3>";
	echo "Para dar salida al proyecto a los usuarios.
	<br><br> Ejemplo:<br> 
<pre>
	-Ejemplo de virtualhost, en apache 2.4 modificar el fichero apache/conf/extra/httpd-vhosts.conf tal que:
		&#60;VirtualHost *:80&#62;
			DocumentRoot \"[ruta a espacio web]/web\"
			DirectoryIndex app.php
			ServerName [dominio de tu web]
		&#60;Directory \"[ruta a espacio web]/web\"&#62;
			AllowOverride All
			Order allow,deny
			Allow from All
		&#60;/Directory&#62;
		&#60;/VirtualHost&#62;
		
	-Y por ultimo descomentar \"Include conf/extra/httpd-vhosts.conf\" en el fichero apache/conf/httpd.conf, para permitir el uso de virtualhosts.
</pre>";
}
?>