<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Service\Session;
use Slim\Psr7\Response;

final class AuthMiddleware implements MiddlewareInterface
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->session->get('shop')) {
            $response = new Response(401);
            return $response;
        }

        return $handler->handle($request);
    }
}