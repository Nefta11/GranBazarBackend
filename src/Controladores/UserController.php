<?php

namespace App\Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Modelos\User;
use App\Lib\Auth;

class UserController
{
    public function register(Request $req, Response $res, $args)
    {
        $parametros = json_decode($req->getBody()->getContents());

        if (!$parametros) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Datos no válidos.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        $user = User::where('email', $parametros->email)->first();

        if ($user) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'El correo ya está registrado.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        try {
            $newUser = new User();
            $newUser->first_name = $parametros->first_name;
            $newUser->last_name = $parametros->last_name;
            $newUser->email = $parametros->email;
            $newUser->phone = $parametros->phone;
            $newUser->birthday = $parametros->birthday;
            $newUser->password = password_hash($parametros->password, PASSWORD_DEFAULT);
            $newUser->save();

            $res->getBody()->write(json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente.']));
            return $res->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Error al registrar usuario.']));
            return $res->withHeader('Content-type', 'application/json');
        }
    }

    public function auth(Request $req, Response $res, $args)
    {
        $parametros = json_decode($req->getBody()->getContents());

        $user = User::where('email', $parametros->email)->first();

        if (!$user) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Correo incorrecto.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        if (!password_verify($parametros->password, $user->password)) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        $token = Auth::addToken(['id' => $user->id]);

        $res->getBody()->write(json_encode(['success' => true, 'message' => 'Inicio de sesión correcto', 'token' => $token]));
        return $res->withHeader('Content-type', 'application/json');
    }
}
