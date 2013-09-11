eCloud

Tu servidor de descargas


En Construcción...


Comandos GIT

git init
git add .
git push https://.....git

git commit -a -m "cambios"
git push -u origin master


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