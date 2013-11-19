<h1>eCloud</h1>

<h2>Tu servidor de descargas -> En construcción, todavia no es funcional.</h2>


Requisitos
<ul>
	<li>Windows.</li>
	<li>Habilitar las extensiones php_fileinfo y php_openssl.</li>
	<li>Fichero php.ini. En el caso de wampserver vigilar que el fichero php.ini sea el que toca.
		<ul>
			<li>memory_limit = 6728M 				<- Limite de memoria.</li>
			<li>upload_max_filesize 400000M		<- El tamaño maximo del fichero a recibir.</li>
			<li>post_max_size 400000M				<- El tamaño maximo de datos a recibir del metodo POST usado en los formularios de envios de datos del navegador.</li>
			<li>max_execution_time = 14400.		<- Tiempo de ejecucion maxima de los scripts php, necesario que sea alto debido a que mientras esta recibiendo ficheros el script sigue en ejecucion.</li>
			<li>xdebug.max_nesting_level = 250		<- Para permitir la mas anidaciones en las funciones. En caso de no estar por defecto, incluirla.</li>
		</ul>
	</li>
</ul>

<h3>Instalación de eCloud.</h3>
<ol>
	<li> Descargar el código de github.com/amiguelc/eCloud y descomprimirlo en el espacio web. Renombrar el fichero app/config/parameters.yml-dist a parameters.yml. Configurar en este fichero la BBDD.</li>
	<li> Ejecutar php app/console check.php, para ver si el servidor cumple los requisitos.</li>
	<li> Modificar el fichero app/config/config.yml para configurar la carpeta de ficheros global llamada var_archivos (y crear la oorrespondiente carpeta en el sistema) y limite-default que es el limite de bytes por usuario.</li>
	<li> Ejecutar Composer "curl -s https://getcomposer.org/installer | php" y "php composer.phar install" para buscar las dependencias y vendors. Obligatorio tener la extension openssl de php habilitada.</li>
	<li> Crear BBDD llamada symfony y luego ejecutar "php app/console doctrine:schema:create".</li>
	<li> Instalar los archivos de la web en su carpeta adeacuada con el comando "php app/console assets:install web".</li>
	<li> Generar CSS Y JS "php app/console assetic:dump --env=prod –no-debug"</li>
	<li> Configurar el usuario/contraseña administrador en app/config/security.yml por defecto admin:admin1.</li>
	<li> Configurar el Virtualhost de Apache de la siguiente manera:</li>
</ol>
<pre>
	-Ejemplo de virtualhost, en apache 2.4 modificar el fichero apache/conf/extra/httpd-vhosts.conf tal que:
		<VirtualHost *:80>
			DocumentRoot "[ruta a espacio web]\symfony\web"
			DirectoryIndex app.php
			ServerName ecloud
		<Directory "[ruta a espacio web]\symfony\web">
			AllowOverride All
			Order allow,deny
			Allow from All
		</Directory>
		</VirtualHost>
		
	-Y por ultimo descomentar "Include conf/extra/httpd-vhosts.conf" en el fichero apache/conf/httpd.conf, para permitir el uso de virtualhosts.
</pre>
<pre>
Base de datos 
Usuarios: 
Ficheros: 
Enlaces: 
Eventos: 
</pre>