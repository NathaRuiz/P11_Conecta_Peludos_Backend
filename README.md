# 🐾 Bienvenido a ConectaPeludos 
<img src="./public/images/Logo-Azul.svg" width="200" alt="Conecta peludos Logo">

## Descripción de la Aplicación 📱
ConectaPeludos es una aplicación web diseñada para ser intuitiva y fácil de usar. Permite a refugios y protectoras de animales en toda España registrarse y gestionar de manera eficiente la información sobre los animales disponibles para adopción. Los posibles adoptantes pueden explorar perfiles detallados de animales, filtrar según sus preferencias y ponerse en contacto directo con las organizaciones. La aplicación también incluye recursos educativos sobre la adopción responsable y proporciona soporte continuo para garantizar la felicidad y el bienestar de los animales adoptados. 
<br><br>

## Contexto/Necesidad
La iniciativa surge como respuesta a la creciente necesidad de proporcionar una solución integral para la adopción de animales de refugios y protectoras. Aunque existen diversas organizaciones dedicadas al cuidado de animales, la falta de una plataforma centralizada limita la visibilidad y accesibilidad para aquellos que desean adoptar. Con la creciente conciencia sobre la adopción responsable y el bienestar animal, se identifica una oportunidad para crear una plataforma que agilice el proceso de adopción y promueva un compromiso a largo plazo entre los nuevos propietarios y sus mascotas.
<br>

## 🛠️ Tecnología:
- Laravel Breeze: Para la gestión de autenticación y autorización.
- Laravel Sanctum: Para la autenticación de API y la gestión de tokens de acceso.
- Cloudinary: Para el almacenamiento y procesamiento de imágenes.
- Mailtrap: Para probar el envio de emails.
<br>

## 💻 Pasos de Instalación:
1. Descargar e Instalar Node.js: Visita el sitio [web oficial](https://nodejs.org/en)  y descarga la versión compatible con tu sistema operativo. Sigue las instrucciones de instalación proporcionadas en el sitio web.

2. Descargar e Instalar XAMPP: Visita el sitio [web oficial](https://www.apachefriends.org/index.html) de XAMPP y descarga la versión compatible con tu sistema operativo (Windows, macOS o Linux). Sigue las instrucciones de instalación proporcionadas en el sitio web.

3. Descargar e Instalar Composer: Visita el sitio [web oficial](https://getcomposer.org/) y descarga la versión compatible con tu sistema operativo. Sigue las instrucciones de instalación proporcionadas en el sitio web.

4. Iniciar XAMPP y Apache: Después de instalar XAMPP, inicia la aplicación. Inicia el servidor Apache desde el panel de control de XAMPP.

5. Puedes utilizar una base de datos compatible (por ejemplo, MySQL, PostgreSQL). En nuestro caso, se utiliza MySQL. Abre PHPMyAdmin en tu navegador web y crea la base de datos con la que vas a trabajar, por ejemplo:
- Haz clic en "SQL" en la barra de navegación superior.
- En la ventana de consulta SQL, pega el código SQL proporcionado:
```sql
CREATE DATABASE conecta_peludos;
```
6. [Clonar](https://docs.github.com/es/repositories/creating-and-managing-repositories/cloning-a-repository) el repositorio.

7. Coloca el proyecto en la carpeta htdocs:

8. En XAMPP, el directorio principal del servidor web es htdocs. Coloca el proyecto en este directorio.
Abre el proyecto en Visual Studio Code (un editor de código fuente desarrollado por Microsoft para Windows, Linux, macOS y Web).

9. Abre la terminal del proyecto en Visual Studio Code y ejecuta `composer install`.

10. Luego copia el archivo de configuración con el siguiente comando `cp .env.example .env` Configura tu archivo .env con los detalles de tu entorno, como la conexión a la base de datos.

11. Luego debes generar la Clave de Aplicación con el siguiente comando `php artisan key:generate`.

12. Luego ejecuta las Migraciones y los Seeds con el siguiente comando `php artisan migrate --seed`. Esto configurará la estructura de la base de datos, creará las tablas necesarias y las poblara con los datos de prueba predefinidos en el proyecto.

13. Iniciar el Servidor de Desarrollo: Ejecuta el siguiente comando php artisan serve. La aplicación estará disponible en http://localhost:8000 (o en el puerto especificado por la consola).
14. Ir al Repositorio del [Fronted](https://github.com/NathaRuiz/P11_Conecta_Peludos_Frontend) y continuar con con los pasos.

### 🖼️ Uso de Cloudinary:
1. Crear una cuenta en Cloudinary: Visita el sitio web de Cloudinary y crea una cuenta si aún no tienes una.

2. Obtener las credenciales de Cloudinary: Después de registrarte en Cloudinary, obtén las credenciales de API necesarias (cloud name, API key, API secret).

3. Configurar las credenciales de Cloudinary en tu aplicación Laravel:

Abre tu archivo `.env` y agrega las siguientes variables de entorno:
```sql
CLOUDINARY_CLOUD_NAME=nombre_de_tu_nube
CLOUDINARY_API_KEY=tu_api_key
CLOUDINARY_API_SECRET=tu_api_secret
```
4. Instalar el paquete Cloudinary en tu proyecto Laravel: Ejecuta el siguiente comando en la terminal de tu proyecto: `composer require cloudinary-labs/cloudinary-laravel`
5. Usar Cloudinary en tu aplicación: Ahora puedes usar Cloudinary para almacenar y gestionar imágenes en tu aplicación Laravel. Consulta la documentación oficial de Cloudinary para obtener más información sobre cómo cargar y manipular imágenes.

### 📧 Uso de Mailtrap:
1. Crear una cuenta en Mailtrap.

2. Obtener las credenciales de Mailtrap: Después de registrarte en Mailtrap, obtén las credenciales SMTP proporcionadas por Mailtrap (SMTP username, SMTP password).

3. Configurar las credenciales de Mailtrap en tu aplicación Laravel:

- Abre tu archivo `.env` y agrega las siguientes variables de entorno:
```sql
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=tu_username_smtp
MAIL_PASSWORD=tu_contraseña_smtp
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_dirección_de_correo_electrónico
MAIL_FROM_NAME="${APP_NAME}"
```
4. Configurar el entorno de Mailtrap en tu archivo config/mail.php: Asegúrate de que el driver SMTP esté configurado para usar Mailtrap en tu entorno de desarrollo.
```sql
'driver' => env('MAIL_MAILER', 'smtp'),
'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
'port' => env('MAIL_PORT', 2525),
'username' => env('MAIL_USERNAME'),
'password' => env('MAIL_PASSWORD'),
'encryption' => env('MAIL_ENCRYPTION', 'tls'),
```

### 📁 Base de Datos de Prueba para Tests:
1. Configurar la base de datos de prueba:

2. En tu archivo `.env`, configura una base de datos específica para pruebas:
```sql
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=conecta_peludos_testing
DB_USERNAME=root
DB_PASSWORD=
```
3. Ejecutar las migraciones para la base de datos de prueba:

4. Ejecuta el siguiente comando en la terminal para migrar la estructura de la base de datos para las pruebas: `php artisan migrate --env=testing`
5. Configurar el entorno de prueba en el archivo phpunit.xml:
- Asegúrate de que el archivo phpunit.xml incluya la configuración para el entorno de prueba:
```sql
<env name="APP_ENV" value="testing"/>
<env name="DB_CONNECTION" value="testing"/>
```
## 📜License

Este proyecto está licenciado bajo los términos de la licencia MIT. Esto significa que puedes usar, copiar, modificar y distribuir el código libremente, siempre y cuando reconozcas la autoría original y no lo utilices con fines comerciales.

## 👩‍💻Author
Created with 💜 by:
- [NathaliaRuiz](https://github.com/NathaRuiz)
