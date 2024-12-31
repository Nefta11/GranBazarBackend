<?php

require 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Datos de conexión
$host = 'localhost';
$dbname = 'bd_granBazar';
$username = 'root';
$password = '1234567';

// Crear la base de datos si no existe
try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}

$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $host,
    'database' => $dbname,
    'username' => $username,
    'password' => $password,
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

if (!Capsule::schema()->hasTable('users')) {
    Capsule::schema()->create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('last_name');
        $table->string('email')->unique();
        $table->string('phone');
        $table->string('birthday_unix'); 
        $table->string('password');
        $table->timestamps();
    });
}

if (!Capsule::schema()->hasTable('categories')) {
    Capsule::schema()->create('categories', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->timestamps();
    });

}
    Capsule::table('categories')->insert([
        ['name' => 'Hombres'],
        ['name' => 'Mujeres'],
        ['name' => 'Niñ@s'],
        ['name' => 'Unisex'],
        ['name' => 'Accesorios'],
        ['name' => 'Deportiva'],
        ['name' => 'Infantil'],
        ['name' => 'Juvenil']
    ]);
    


if (!Capsule::schema()->hasTable('products')) {
    Capsule::schema()->create('products', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->text('description');
        $table->decimal('price', 8, 2);
        $table->unsignedInteger('category_id');
        $table->text('image');
        $table->integer('stock')->default(0);
        $table->boolean('status')->default(true);
        $table->timestamps();

        $table->foreign('category_id')->references('id')->on('categories');
    });
}