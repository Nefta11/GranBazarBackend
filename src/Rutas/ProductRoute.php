<?php

use App\Controladores\ProductController;
use Slim\App;
use App\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

$authMiddleware = new AuthMiddleware();

return function (App $app) use ($authMiddleware) {
    $app->group('/api/products', function (RouteCollectorProxy $group) {
        $group->post('', ProductController::class . ':create');
        $group->get('', ProductController::class . ':getAll');
        $group->put('/{id}', ProductController::class . ':update');
        $group->delete('/{id}', ProductController::class . ':delete');
    })->add($authMiddleware);
};
