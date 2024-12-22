<?php

use App\Controladores\ProductController;
use Slim\App;
use App\Middleware\AuthMiddleware;

return function (App $app) {
    $app->group('/api/products', function (\Slim\Routing\RouteCollectorProxy $group) {
        $group->post('', ProductController::class . ':create')->add(new AuthMiddleware());
        $group->get('', ProductController::class . ':getAll');
        $group->put('/{id}', ProductController::class . ':update')->add(new AuthMiddleware());
        $group->delete('/{id}', ProductController::class . ':delete')->add(new AuthMiddleware());
    });
};
