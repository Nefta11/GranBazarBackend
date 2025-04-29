<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Lib\Auth;

class AuthMiddleware
{
    /**
     * Middleware de autenticación para verificar el token JWT.
     * 
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Manejar CORS y permitir encabezados personalizados
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response();
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, auth-token')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        }

        // Obtener el encabezado de autorización
        $authorizationHeader = $request->getHeaderLine('auth-token') ?: $request->getHeaderLine('Authorization');
        $token = null;

        // Extraer el token del encabezado si está presente
        if (!empty($authorizationHeader)) {
            $token = str_replace('Bearer ', '', $authorizationHeader); // Eliminar el prefijo 'Bearer ' si existe
        }

        // Validar el token
        $authenticated = Auth::validateToken($token);

        // Si el token no es válido, retornar una respuesta 401 (No autorizado)
        if (!$authenticated) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                "result"     => null,
                "response"   => false,
                "message"    => "Unauthorized"
            ]));
            return $response->withStatus(401)
                            ->withHeader('Content-Type', 'application/json')
                            ->withHeader('Access-Control-Allow-Origin', '*');
        }

        // Si el token es válido, continuar con el manejo de la solicitud
        return $handler->handle($request);
    }
}
