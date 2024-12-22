<?php
use App\Controllers\UserController;

$app->group('/api', function () {
    $this->post('/register', UserController::class . ':register');
    $this->post('/auth', UserController::class . ':auth');
});