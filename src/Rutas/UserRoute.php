<?php

use App\Controllers\UserController;
use Slim\App;

return function (App $app) {
    $app->group('/api', function () {
        $this->post('/register', UserController::class . ':register');
        $this->post('/auth', UserController::class . ':auth');
    });
};
