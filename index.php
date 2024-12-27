<?php

require 'vendor/autoload.php'; // Cargar dependencias de Composer
require 'src/Config/database.php'; // Cargar configuración de Eloquent

use Slim\Factory\AppFactory;
use DI\Container;
use App\Middleware\AuthMiddleware;

// Crear el contenedor de dependencias
$container = new Container();
AppFactory::setContainer($container);

// Crear la aplicación Slim
$app = AppFactory::create();

// Configuración de Slim
$app->addErrorMiddleware(true, true, true);

// Middleware para manejar CORS
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);

    // Configurar encabezados para CORS
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:3000') // Cambia esto si el front se despliega en otro dominio
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true'); // Agregar para habilitar cookies si es necesario
});

// Manejar solicitudes OPTIONS para CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:3000') // Asegúrate de que coincida con el dominio permitido
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Cargar rutas dinámicamente
$routeFiles = glob(__DIR__ . '/src/Rutas/*.php'); // Escanear archivos de rutas en la carpeta
foreach ($routeFiles as $routeFile) {
    (require $routeFile)($app); // Cargar cada archivo de rutas
}

// Ejecutar la aplicación Slim
$app->run();
