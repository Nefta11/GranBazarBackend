<?php

namespace App\Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Modelos\User; // Asegúrate de que esta ruta sea correcta
use Illuminate\Database\Capsule\Manager as Capsule;

class UserController
{
    public function register(Request $req, Response $res, $args)
    {
        $parametros = json_decode($req->getBody()->getContents());
        error_log(print_r($parametros, true)); // Registro de depuración

        if (!$parametros) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Datos no válidos.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        $user = User::where('email', $parametros->email)->first();
        error_log(print_r($user, true)); // Registro de depuración

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
            error_log('Usuario registrado: ' . print_r($newUser, true)); // Registro de depuración

            $res->getBody()->write(json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente.']));
            return $res->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            error_log('Error al registrar usuario: ' . $e->getMessage()); // Registro de depuración
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Error al registrar usuario.']));
            return $res->withHeader('Content-type', 'application/json');
        }
    }

    public function auth(Request $req, Response $res, $args)
    {
        $parametros = json_decode($req->getBody()->getContents());
        error_log(print_r($parametros, true)); // Registro de depuración

        $user = User::where('email', $parametros->email)->first();
        error_log(print_r($user, true)); // Registro de depuración

        if (!$user) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Correo incorrecto.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        if (!password_verify($parametros->password, $user->password)) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        // Aquí puedes generar un token JWT o cualquier otro mecanismo de autenticación
        $token = 'token_de_ejemplo'; // Reemplaza esto con la lógica para generar el token

        $res->getBody()->write(json_encode(['success' => true, 'message' => 'Inicio de sesión correcto', 'token' => $token]));
        return $res->withHeader('Content-type', 'application/json');
    }
}
