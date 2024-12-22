<?php

use App\Controladores\UserController;
use Slim\App;
use App\Middleware\AuthMiddleware;

return function (App $app) {
    $app->group('/api', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->post('/register', UserController::class . ':register');
        $group->post('/auth', UserController::class . ':auth');
        
        // Aplica el middleware de autenticación a las rutas que lo necesiten
        $group->group('', function (\Slim\Routing\RouteCollectorProxy $authGroup) {
            // Añade aquí las rutas que requieren autenticación
            // Ejemplo:
            // $authGroup->get('/profile', UserController::class . ':profile');
        })->add(new AuthMiddleware());
    });
};
