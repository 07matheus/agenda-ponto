<?php

namespace AgendaPonto\Middlewares;

use AgendaPonto\Configs\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Classe responsável por impedir que o usuário acesse recursos de quem não efetuou login
 */
class ForceLoginMiddleware {
  /**
   * @param  Request        $request PSR-7 request
   * @param  RequestHandler $handler PSR-15 request handler
   * @return Response
   */
  public function __invoke(Request $request, RequestHandler $handler): Response {
    $response = $handler->handle($request);
    $usuario  = (new Session)->get(['usuario']);

    if(!is_null($usuario) || is_numeric($usuario['id'] ?? null)) {
      header('Location: /dashboard');
      exit;
    }

    return $response;
  }
}
