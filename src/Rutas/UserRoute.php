<?php

use App\Controladores\UserController;
use Slim\App;
use App\Middleware\AuthMiddleware;

return function (App $app) {
    $app->group('/api', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->post('/register', UserController::class . ':register');
        $group->post('/auth', UserController::class . ':auth');
        $group->get('/user/{id}', UserController::class . ':getUser'); // Agregar esta línea
    });
};
