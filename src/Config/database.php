<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',          // Cambia esto si usas otro host
    'database' => 'bd_granBazar',      // Nombre de tu base de datos
    'username' => 'root',        // Tu usuario de MySQL
    'password' => '1234567',     // Tu contraseña de MySQL
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

// Hacer que Eloquent esté disponible globalmente
$capsule->setAsGlobal();
$capsule->bootEloquent();
