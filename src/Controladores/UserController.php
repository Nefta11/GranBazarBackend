<?php

namespace App\Controladores;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Modelos\User;
use App\Lib\Auth;
use Google_Client;

class UserController
{
    private function jsonResponse(Response $res, array $data, int $status = 200): Response
    {
        $res->getBody()->write(json_encode($data));
        return $res->withHeader('Content-type', 'application/json')->withStatus($status);
    }
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

        $token = Auth::addToken([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name
        ]);

        $userData = $user->toArray();
        $userData['birthday'] = date('d-m-Y', $user->birthday_unix); 

        $res->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Inicio de sesión correcto',
            'token' => $token,
            'user' => $userData
        ]));
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
            return $this->jsonResponse($res, ['success' => false, 'message' => 'Datos no válidos.'], 400);
        }

        $parametros = json_decode($body, false);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->jsonResponse($res, ['success' => false, 'message' => 'JSON no válido.'], 400);
        }

        $tokenId = $parametros->tokenId ?? null;
        if (!$tokenId) {
            return $this->jsonResponse($res, ['success' => false, 'message' => 'El token es requerido.'], 400);
        }

        // Instanciamos el cliente de Google y deshabilitamos la verificación SSL para desarrollo
        $client = new Google_Client(['client_id' => '631817857538-ps3dn27d32dp5kc106ri0sr347ubp4ls.apps.googleusercontent.com']);
        $client->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));  // Deshabilitar SSL en desarrollo

        try {
            $ticket = $client->verifyIdToken($tokenId);
            if (!$ticket) {
                return $this->jsonResponse($res, ['success' => false, 'message' => 'Autenticación con Google fallida.'], 401);
            }

            // Aquí $ticket es un array. Trabajamos directamente con sus valores.
            $payload = $ticket['payload'] ?? null;

            if (!$payload || !is_array($payload) || !isset($payload['email'])) {
                return $this->jsonResponse($res, ['success' => false, 'message' => 'Payload inválido de Google.'], 400);
            }

            // Buscar o crear el usuario
            $user = User::where('email', $payload['email'])->first();
            if (!$user) {
                $user = new User();
                $user->first_name = $payload['given_name'] ?? 'Usuario';
                $user->last_name = $payload['family_name'] ?? '';
                $user->email = $payload['email'];
                $user->save();
            }

            // Generar token
            $token = Auth::addToken([
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ]);

            return $this->jsonResponse($res, ['success' => true, 'token' => $token]);
        } catch (\Exception $e) {
            error_log($e->getMessage()); // Registrar el error para depuración
            return $this->jsonResponse($res, ['success' => false, 'message' => 'Error en la autenticación con Google.'], 500);
        }
    }
}
