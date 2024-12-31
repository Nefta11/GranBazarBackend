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
        // Obtener el encabezado de autorización
        $authorizationHeader = $request->getHeaderLine('auth-token');
        $token = $authorizationHeader;

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
            return $response->withStatus(401)->withHeader('Content-type', 'application/json');
        }

        // Si el token es válido, continuar con el manejo de la solicitud
        return $handler->handle($request);
    }
}
