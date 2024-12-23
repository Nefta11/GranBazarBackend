<?php

namespace App\Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Modelos\User;
use App\Lib\Auth;
use Google_Client;

class UserController
{
    public function register(Request $req, Response $res, $args)
    {
        $body = $req->getBody()->getContents();
        if (empty($body)) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Datos no válidos.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        $parametros = json_decode($body, false); // Cambiar a false para obtener un objeto

        if (json_last_error() !== JSON_ERROR_NONE) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'JSON no válido.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        // Validar formato de email
        if (!filter_var($parametros->email, FILTER_VALIDATE_EMAIL)) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Formato de correo no válido.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        // Validar número de teléfono
        if (!preg_match('/^\d{10}$/', $parametros->phone)) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'El número de teléfono debe tener 10 dígitos.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        // Validar formato de fecha de nacimiento
        $fecha = \DateTime::createFromFormat('d/m/Y', $parametros->birthday);
        if (!$fecha || $fecha->format('d/m/Y') !== $parametros->birthday) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Formato de fecha de nacimiento no válido DD-MM-YYYY.']));
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
            $newUser->birthday_unix = $fecha->getTimestamp(); // Almacenar fecha en formato Unix
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
        $body = $req->getBody()->getContents();
        if (empty($body)) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Datos no válidos.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        $parametros = json_decode($body, false); // Cambiar a false para obtener un objeto

        if (json_last_error() !== JSON_ERROR_NONE) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'JSON no válido.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        $user = User::where('email', $parametros->email)->first();

        if (!$user) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Correo incorrecto.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        if (!password_verify($parametros->password, $user->password)) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        // Incluir id, nombre y apellido del usuario en el token
        $token = Auth::addToken([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name
        ]);

        $res->getBody()->write(json_encode(['success' => true, 'message' => 'Inicio de sesión correcto', 'token' => $token]));
        return $res->withHeader('Content-type', 'application/json');
    }

    public function getUser(Request $req, Response $res, $args)
    {
        $user = User::find($args['id']);

        if (!$user) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Usuario no encontrado.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        $userData = $user->toArray();
        $userData['birthday'] = date('d-m-Y', $user->birthday_unix); // Convertir fecha de Unix a formato DD-mm-yyyy

        $res->getBody()->write(json_encode(['success' => true, 'user' => $userData]));
        return $res->withHeader('Content-type', 'application/json');
    }

    public function googleAuth(Request $req, Response $res, $args)
    {
        $body = $req->getBody()->getContents();
        if (empty($body)) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Datos no válidos.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        $parametros = json_decode($body, false); // Cambiar a false para obtener un objeto

        if (json_last_error() !== JSON_ERROR_NONE) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'JSON no válido.']));
            return $res->withHeader('Content-type', 'application/json');
        }

        $tokenId = $parametros->tokenId;

        $client = new Google_Client(['client_id' => getenv('CLIENT_ID_WEB')]); 
        try {
            $ticket = $client->verifyIdToken($tokenId);
            if (!$ticket) {
                $res->getBody()->write(json_encode(['success' => false, 'message' => 'Google authentication failed']));
                return $res->withHeader('Content-type', 'application/json');
            }
            $payload = (object) $ticket->getPayload(); // Convertir a objeto

            if (!$payload || !isset($payload->email)) {
                $res->getBody()->write(json_encode(['success' => false, 'message' => 'Google authentication failed']));
                return $res->withHeader('Content-type', 'application/json');
            }

            $user = User::where('email', $payload->email)->first();

            if (!$user) {
                $user = new User();
                $user->first_name = $payload->given_name;
                $user->last_name = $payload->family_name;
                $user->email = $payload->email;
                $user->save();
            }

            $token = Auth::addToken([
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name
            ]);

            $res->getBody()->write(json_encode(['success' => true, 'token' => $token]));
            return $res->withHeader('Content-type', 'application/json');
        } catch (\Exception $e) {
            $res->getBody()->write(json_encode(['success' => false, 'message' => 'Error with Google authentication']));
            return $res->withHeader('Content-type', 'application/json');
        }
    }
}
