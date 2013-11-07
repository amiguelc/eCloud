eCloud

Tu servidor de descargas -> En construcción, todavia no es funcional.


Requisitos
	-Windows.
	-Habilitar las extensiones php_fileinfo y php_openssl.
	-Fichero php.ini. En el caso de wampserver vigilar que el fichero php.ini sea el que toca.
				-memory_limit = 6728M 				<- Limite de memoria.
				-upload_max_filesize 400000M		<- El tamaño maximo del fichero a recibir.
				-post_max_size 400000M				<- El tamaño maximo de datos a recibir del metodo POST usado en los formularios de envios de datos del navegador.
				-max_execution_time = 14400.		<- Tiempo de ejecucion maxima de los scripts php, necesario que sea alto debido a que mientras esta recibiendo ficheros el script sigue en ejecucion.

Instalación de eCloud.

1 - Descargar el código de github.com/amiguelc/eCloud y descomprimirlo en el espacio web. Renombrar el fichero app/config/parameters.yml-dist a parameters.yml
2 - Modificar el fichero app/config/config.yml para configurar BBDD y carpeta de ficheros global llamada var_archivos (y crear la oorrespondiente carpeta en el sistema) y limite-default que es el limite de bytes por usuario.
3 - Ejecutar Composer "curl -s https://getcomposer.org/installer | php" y "php composer.phar install" para buscar las dependencias y vendors. Obligatorio tener la extension openssl de php habilitada.
4 - Crear BBDD llamada symfony y luego ejecutar "php app/console doctrine:schema:create".
5 - Generar CSS Y JS "php app/console assetic:dump --env=prod –no-debug"
6 - Configurar el usuario/contraseña administrador en app/config/security.yml por defecto admin:admin1.
7 – Configurar el Virtualhost de Apache de la siguiente manera:
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

Base de datos 
Usuarios: 
Ficheros: 
Enlaces: 
Eventos: 