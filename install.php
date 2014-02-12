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

if ($_SERVER['REMOTE_ADDR']!="127.0.0.1"){echo "Página accesible solo desde la ip 127.0.0.1."; die();}

if (!isset($_GET['paso'])){$_GET['paso']=1;}

echo "<h2>Instalador de eCloud</h2>";
echo "<hr></hr>";

//Primer paso

if ($_GET['paso']==1){

	echo "<h3>Primer paso: Configurar ficheros</h3>";
	echo "<b>Nota</b>: Estos ficheros de configuracion estan creados con el formato YAML, modificar un simple espacio destrozaria la configuracion. <br>Por eso se recomienda tan solo modificar lo necesario y respetar la estructura.<br>";
	if (PHP_OS=="Linux"){echo "Ten en cuenta que en Linux el servidor Apache necesita permisos de escritura sobre estos ficheros para el usuario www-data.<br>";}
	
	if(isset($_POST["recup"])){
		if (!copy("app/config/parameters.yml.dist", "app/config/parameters.yml")) {
			echo "<br>Error al copiar del fichero app/config/parameters.yml.dist...<br>";
		}else{
			echo "<br>Recuperado fichero original app/config/parameters.yml.";
		}
	}
	//hash('sha1', uniqid(mt_rand()));
	if(isset($_POST["q"])){
		if (is_writable("app/config/parameters.yml")){$asd=file_put_contents("app/config/parameters.yml", $_POST['q']);}else{ echo "El fichero no tiene permisos de escritura para el usuario de apache<br>";$asd=FALSE;}
	}
	
	if (!file_exists("app/config/parameters.yml")) {
		if (!copy("app/config/parameters.yml.dist", "app/config/parameters.yml")) {
			echo "<br>Error al copiar del fichero app/config/parameters.yml.dist...<br>";
		}else{
			echo "<br>Copiado fichero de app/config/parameters.yml-dist a app/config/parameters.yml.";
		}
	}
	///////////////////////////////parameters.yml////////////////////////////////////////////////////////////////////////////////////////////////
	echo "<h4> Fichero app/config/parameters.yml para configurar la base de datos y el secret token.</h4>";
	if (!is_writable("app/config/parameters.yml")){echo "<span style='color:red;'>El fichero no tiene permisos de escritura para el usuario de apache.</span><br>";}
	$parameters=file_get_contents("app/config/parameters.yml");
	echo "<form name='parameters' action='' method='post' ><textarea name='q' cols='100' rows='15'>".$parameters."</textarea>";
	echo "<br><input type='submit' value='Guardar' ></form>";
	echo "<form name='recuperar_parameters' action='' method='post' ><input name='recup' type='submit' value='Recuperar original'></form>";
	
	if(isset($_POST["q"]) && $asd!=FALSE){ echo "Datos guardados.";}
	
	
	/////////////////////////////////config.yml/////////////////////////////////////////////////////////////////////////////////////////////////
	echo "<h4> Fichero app/config/config.yml para configurar las variables var_archivos y default_limite</h4>"; //FALTA: Crear la carpeta var_archivos o comprobar su creacion y permisos.
	$writ1=TRUE;;
	if (!is_writable("app/config/config.yml")){echo "<span style='color:red;'>El fichero no tiene permisos de escritura para el usuario de apache.</span><br>";$writ1=FALSE;}
	$config="";
	$config_resum="\r\n\r\n...fichero resumido... \r\n\r\n";
	if (file_exists("app/config/config.yml")){
		$fichero = @fopen('app//config/config.yml', 'rb', true );
		if(!$fichero){
		   $result.= 'No se puede abrir el fichero.';
		}
		$num=1;
		while (!feof($fichero)){

			 $linea = fgets ($fichero) ;
			if ($num==73){
				if (isset($_POST['var_archivos']) && $writ1==TRUE){
					$var_archivos=$_POST['var_archivos'];
					if($var_archivos[strlen($var_archivos)-1]!="\\"){
						if($var_archivos[strlen($var_archivos)-1]!="/"){
							$var_archivos.="/";
						}
					}
					$pieces = explode(":", $linea);
					$linea=$pieces[0].": ".trim($var_archivos)."\r\n";
				}else{
					$var_archivos=trim(substr($linea,17));
				}
			}
			if ($num==74){
				if (isset($_POST['default_limite']) && $writ1==TRUE){
					$default_limite=$_POST['default_limite'];
					$pieces = explode(":", $linea);
					$linea=$pieces[0].": ".trim($default_limite)."\r\n";
				}else{
					$default_limite=trim(substr($linea,19));
				}
			}

			if ($num>68){$config_resum.=$linea;}
			$config.=$linea;
			$num++;
		}
	}

	if(isset($_POST["var_archivos"]) || isset($_POST["default_limite"])){
		if (is_writable("app/config/config.yml")){$asd2=file_put_contents("app/config/config.yml", $config);}else{$asd2=FALSE;}
	}
	
	echo "<textarea cols='100' rows='15'>".$config_resum."</textarea>";
	echo "<br><form name='config' action='' method='post'><table><tr><td>var_archivos</td><td> <input type='text' name='var_archivos' value='";if(isset($var_archivos)){echo $var_archivos;} echo "'> </td><td> Carpeta raiz desde donde colgaran todos los ficheros de eCloud.</td></tr>";
	echo "<br><tr><td>default_limite</td><td> <input type='text' name='default_limite' value='";if(isset($default_limite)){echo $default_limite;} echo "'> </td><td> El limite de bytes por defecto por cada cuenta.</td></tr></table>";
	echo "<br><input type='submit' value='Guardar'> </form>";
	if(isset($_POST["var_archivos"]) || isset($_POST['default_limite'])){ if($asd2!=FALSE){echo "Datos guardados.";}}
	
	//Comprobacion de var archivos.
	if (!file_exists($var_archivos) || !is_writable($var_archivos)){echo "<br><span style='color:red;'> No existe o no se tiene permisos sobre la carpeta ".$var_archivos.".</span>";}
	
	//////////////////////////////////////////////////////////////security////////////////////////////////////////////////////
	echo "<h4> Fichero app/config/security.yml para configurar la contraseña de administrador por defecto admin:admin1</h4>";
	$writ2=TRUE;
	if (!is_writable("app/config/security.yml")){echo "<span style='color:red;'>El fichero no tiene permisos de escritura para el usuario de apache.</span><br>";$writ2=FALSE;}
	$resum=FALSE;
	$security="";
	$security_resum="\r\n\r\n...fichero resumido... \r\n\r\n\n";
	if (file_exists("app/config/security.yml")){
		$fichero = @fopen('app//config/security.yml', 'rb', true );
		if(!$fichero){
		   $result.= 'No se puede abrir el fichero.';
		}
		$num=1;
		while (!feof($fichero)){

			$linea = fgets ($fichero) ;
			
			if (preg_match("/^    providers:/", $linea)){$resum=TRUE;}
			if (preg_match("/^    firewalls:/", $linea)){$resum=FALSE;}

			if (preg_match("/^                users:/", $linea)){
			$security.=$linea;
			$security_resum.=$linea;
				$linea = fgets ($fichero);
				if (isset($_POST['admin']) && $writ2==TRUE){
					$admin=$_POST['admin'];
					$pieces = explode(":", $linea, 2);
					$linea="                    ".trim($admin).":".$pieces[1];
				}else{
					$pieces = explode(":", $linea);
					$admin=trim($pieces[0]);
				}
				
				if (isset($_POST['password']) && $writ2==TRUE){
					$password=trim($_POST['password']);
					$pieces = explode(":", $linea, 4);
					$pieces2 = explode(",", $pieces[2], 2);
					$linea=$pieces[0].":".$pieces[1].": ".$password.",".$pieces2[1].":".$pieces[3];
					//$linea=$pieces[0].":"$pieces[1].":".$pieces[2];
				}else{
					$pieces = explode(":", $linea, 4);
					$pieces2 = explode(",", $pieces[2], 2);
					$password=trim($pieces2[0]);
				}
				
			}
			
			if ($resum==TRUE){$security_resum.=$linea;}
			$security.=$linea;
			$num++;
		}
	}

	if(isset($_POST["admin"]) || isset($_POST["password"])){
		if (is_writable("app/config/security.yml")){$asd3=file_put_contents("app/config/security.yml", $security);}else{ $asd3=FALSE;}
	}
	
	
	echo "<textarea cols='100' rows='15'>".$security_resum."</textarea>";
	
	echo "<br><form name='security' action='' method='post'><table><tr><td>Usuario: </td><td> <input type='text' name='admin' value='";if(isset($admin)){echo $admin;} echo "'> </td></tr>";
	echo "<br><tr><td>Password</td><td> <input type='text' name='password' value='";if(isset($password)){echo $password;} echo "'> </td></tr></table>";
	echo "<br><input type='submit' value='Guardar'> </form>";
	if(isset($_POST["admin"]) || isset($_POST['password'])){ if($asd3!=FALSE){echo "Datos guardados.<br>";}}
	
	echo "<br><input type='button' value='Siguiente' onclick=\"location.search='?paso=2'\">";
}

//Segundo paso

if ($_GET['paso']==2){
	echo "<h3>Segundo paso: Cumplir los requisitos</h3>";
	include "check.php";	
	echo "<br><br><input type='button' value='Siguiente' onclick=\"location.search='?paso=3'\">  Nota: El siguiente paso se toma su tiempo en descargar datos, se paciente.";
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
			if (PHP_OS=="Linux") {
				//echo exec('php composer.phar install 2>&1',$retorno,$a);
				$salida=shell_exec("COMPOSER_HOME=bin/comp_tmp php composer.phar install;");
				echo "<pre>$salida</pre>";
				exec("rm -f -R bin/comp_tmp");
			}else{
				$salida = shell_exec('php composer.phar install');
				echo "<pre>$salida</pre>";
			}
		} else {
			echo "No se encuentra el archivo composer.phar";
		}
	} else {
		// an error happened
		echo "Error al descargar el instalador de composer.";
	}

	echo "<h3>Y ejecutar php app/check.php, para ver si el servidor cumple los requisitos del framework Symfony2.</h3>";
	//Entre ellos mirar si cache y logs tienen permisos de escritura.
	$salida = shell_exec('php app/check.php');
	echo "<pre>$salida</pre>";
	
	echo "<br><input type='button' value='Siguiente' onclick=\"location.search='?paso=4'\">";
}

//Cuarto paso
if ($_GET['paso']==4){
	echo "<h3>Cuarto paso: Crear bases de datos, y copiar imagenes y ficheros css y js</h3>";
		echo "php app/console doctrine:database:create [Crea la base de datos]<br>";
		$salida = shell_exec('php app/console doctrine:database:create');
		echo "<pre>$salida</pre><br>";
		
		echo "php app/console doctrine:schema:create [Crea el schema, es decir, las tablas.]<br>";
		$salida = shell_exec('php app/console doctrine:schema:create');
		echo "<pre>$salida</pre><br>";
		$salida = shell_exec('php app/console doctrine:schema:update');
		echo "<pre>$salida</pre><br>";
		
		echo "php app/console assets:install web [Instala imagenes y datos en el directorio web]<br>";
		$salida = shell_exec('php app/console assets:install web');
		echo "<pre>$salida</pre><br>";
		
		echo "php app/console assetic:dump --env=prod [Instala los archivos css y js en el directorio web.]";
		$salida = shell_exec('php app/console assetic:dump --env=prod');
		echo "<pre>$salida</pre><br>";
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
</pre>

<br><br>Finalizado.";
}
?>