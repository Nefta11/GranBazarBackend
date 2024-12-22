<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use App\Lib\Auth;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $authorizationHeader = $request->getHeaderLine('Authorization');
        $token = null;

        if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
            $token = $matches[1];
        }

        $authenticated = Auth::validateToken($token);

        if (!$authenticated) {
            $response = new Response();
            $response->getBody()->write(json_encode([
                "result"     => null,
                "response"   => false,
                "message"    => "Unauthorized"
            ]));
            return $response->withStatus(401);
        }

        return $handler->handle($request);
    }
}
