<?php

use App\Controladores\UserController;
use Slim\App;

return function (App $app) {
    $app->group('/api', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->post('/register', UserController::class . ':register');
        $group->post('/auth', UserController::class . ':auth');
    });
};
