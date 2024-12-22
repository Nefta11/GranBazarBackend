<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;

class UserController
{
    public function register(Request $req, Response $res, $args)
    {
        $parametros = json_decode($req->getBody()->getContents());
        $user = User::where('email', $parametros->email)->first();

        if ($user) {
            return $res->withHeader('Content-type', 'application/json')
                ->getBody()->write(json_encode(['success' => false, 'message' => 'El correo ya está registrado.']));
        }

        $newUser = new User();
        $newUser->first_name = $parametros->first_name;
        $newUser->last_name = $parametros->last_name;
        $newUser->email = $parametros->email;
        $newUser->phone = $parametros->phone;
        $newUser->birthday = $parametros->birthday;
        $newUser->password = password_hash($parametros->password, PASSWORD_DEFAULT);
        $newUser->save();

        return $res->withHeader('Content-type', 'application/json')
            ->getBody()->write(json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente.']));
    }

    public function auth(Request $req, Response $res, $args)
    {
        $parametros = json_decode($req->getBody()->getContents());
        $user = User::where('email', $parametros->email)->first();

        if (!$user) {
            return $res->withHeader('Content-type', 'application/json')
                ->getBody()->write(json_encode(['success' => false, 'message' => 'Correo incorrecto.']));
        }

        if (!password_verify($parametros->password, $user->password)) {
            return $res->withHeader('Content-type', 'application/json')
                ->getBody()->write(json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']));
        }

        // Aquí puedes generar un token JWT o cualquier otro mecanismo de autenticación
        $token = 'token_de_ejemplo'; // Reemplaza esto con la lógica para generar el token

        return $res->withHeader('Content-type', 'application/json')
            ->getBody()->write(json_encode(['success' => true, 'message' => 'Inicio de sesión correcto', 'token' => $token]));
    }
}
