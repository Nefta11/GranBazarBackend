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
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:3000')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

// Cargar rutas
(require 'src/Rutas/UserRoute.php')($app);
(require 'src/Rutas/ProductRoute.php')($app);

$app->run();
