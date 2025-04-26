# Gran Bazar Backend

Este proyecto es una aplicación backend desarrollada con el framework Slim 4 y PHP-DI para la gestión de un bazar. Proporciona una API RESTful para manejar productos, usuarios y categorías, entre otros recursos.

## Requisitos

- PHP 7.4 o superior
- Composer
- Servidor MySQL

## Instalación

1. Clona este repositorio:
   ```bash
   git clone <URL_DEL_REPOSITORIO>
   ```

2. Navega al directorio del proyecto:
   ```bash
   cd gran-bazar-backend
   ```

3. Instala las dependencias con Composer:
   ```bash
   composer install
   ```

4. Configura la base de datos en el archivo `src/Config/database.php`:
   ```php
   'host' => 'localhost',
   'database' => 'bd_granBazar',
   'username' => 'root',
   'password' => '1234567',
   ```

## Crear Tablas en la Base de Datos

Si ya tienes la base de datos creada pero necesitas generar las tablas, puedes ejecutar el siguiente comando:

```bash
php src/Database/schema.php
```

Este comando ejecutará el archivo `schema.php`, que contiene las instrucciones para crear las tablas necesarias en la base de datos configurada.

## Uso

### Servidor de desarrollo

Para iniciar el servidor de desarrollo, ejecuta:
```bash
php -S localhost:8080 -t .
```
Luego, abre `http://localhost:8080` en tu navegador.

### Rutas

Las rutas de la API se cargan dinámicamente desde la carpeta `src/Rutas`. Cada archivo en esta carpeta define un conjunto de rutas relacionadas con un recurso específico, como productos o usuarios.

### Middleware

El proyecto incluye middleware para manejar CORS y autenticación. Puedes encontrar la lógica de autenticación en `src/Middleware/AuthMiddleware.php`.

## Estructura del Proyecto

- `index.php`: Punto de entrada principal de la aplicación.
- `src/Config`: Configuración de la base de datos y otros ajustes.
- `src/Controladores`: Controladores para manejar la lógica de negocio.
- `src/Modelos`: Modelos Eloquent para interactuar con la base de datos.
- `src/Rutas`: Definición de rutas de la API.
- `vendor/`: Dependencias instaladas por Composer.

## Pruebas

Para ejecutar las pruebas, utiliza:
```bash
composer test
```

## Docker

También puedes ejecutar el proyecto con Docker:
```bash
docker-compose up -d
```
Luego, abre `http://localhost:8080` en tu navegador.

## Contribuciones

Si deseas contribuir a este proyecto, por favor abre un issue o envía un pull request.
