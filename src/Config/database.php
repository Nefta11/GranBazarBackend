<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',          
    'database' => 'bd_granBazar',      
    'username' => 'root',        
    'password' => '1234567',     
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

// Hacer que Eloquent esté disponible globalmente
$capsule->setAsGlobal();
$capsule->bootEloquent();
