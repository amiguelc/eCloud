eCloud

Tu servidor de descargas


Requisitos
	-Windows.
	-Habilitar php_fileinfo extension
	-Fichero php.ini.
				-memory_limit = 6728M
				-upload_max_filesize 400000M
				-post_max_size 400000M
				-max_execution_time = 14400.
	-Virtualhost Apache, fichero apache/conf/extra/httpd-vhosts.conf
		<VirtualHost *:80>
			DocumentRoot "D:\wamp\www\symfony\web"
			DirectoryIndex app.php
			ServerName ecloud
		<Directory "D:\wamp\www\symfony\web">
			AllowOverride All
			Order allow,deny
			Allow from All
		</Directory>
		</VirtualHost>
	-Fichero apache/conf/httpd.conf, descomentar "Include conf/extra/httpd-vhosts.conf"


Instalación de eCloud.

1 - Descargar el código de github.com/amiguelc/eCloud
2 - Ejecutar Composer "curl -s https://getcomposer.org/installer | php" y php composer.phar install" para buscar las dependencias y vendors.
3 - Configurar BBDD y carpeta de ficheros global llamada var_archivos con el limite por defecto establecido en 500MB por usuario en app/config/parameters.yml
4 - Crear BBDD "php app/console doctrine:schema:create"
5 - Generar CSS Y JS "php app/console assetic:dump --env=prod –no-debug"
6 - Configurar el usuario administrador en app/config/security.yml
7 – Configurar el Virtualhost de Apache para que apunte a web/app.php, el núcleo de Symfony2.

Fichero de configuración: app/config/parameters.yml

var_archivos: C:\ecloud\ -> Donde se crean las carpetas de los usuarios
default_limite: 524288000 -> Es el límite por defecto cuando se registran los usuarios.

Base de datos 
Usuarios:
Ficheros:
Enlaces:
Eventos: