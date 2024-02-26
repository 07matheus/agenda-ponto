<?php

use AgendaPonto\Controllers\Dashboard\Listagem as MinhasTarefas;
use AgendaPonto\Controllers\Usuario\Cadastro as CadastroUsuario;
use AgendaPonto\Controllers\Usuario\Login as LoginUsuario;
use AgendaPonto\Controllers\Tarefas\Cadastro as CadastroTarefa;
use AgendaPonto\Controllers\Tarefas\Editar as EditarTarefa;
use AgendaPonto\Models\DTOs\UsuarioDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Interfaces\RouteCollectorProxyInterface;

$request  = Request::class;
$response = Request::class;

// ROTAS DO SISTEMA
$route->get('/', function(Request $request, Response $response) {
  $response->getBody()->write(twig('paginas/inicio'));
  return $response;
});

$route->get('/dashboard', function(Request $request, Response $response) {
  return (new MinhasTarefas($response))->viewAll()->getRenderResponse();
});

$route->get('/entrar', function(Request $request, Response $response) {
  return (new LoginUsuario($response))->view()->getRenderResponse();
});

$route->post('/entrar', function(Request $request, Response $response) use ($route) {
  $dtoUsuario   = (new UsuarioDTO)->setDados($request->getParsedBody());
  $obController = new LoginUsuario($response);

  return $obController->save($dtoUsuario)->getRenderResponse();
});

$route->group('/usuario', function(RouteCollectorProxyInterface $group) {
  $group->get('/cadastro', function(Request $request, Response $response) {
    return (new CadastroUsuario($response))->view()->getRenderResponse(new UsuarioDTO);
  });
  
  $group->post('/cadastro', function(Request $request, Response $response) {
    $dtoUsuario   = (new UsuarioDTO)->setDados($request->getParsedBody());
    $obController = new CadastroUsuario($response);
  
    return $obController->save($dtoUsuario)->getRenderResponse($dtoUsuario);
  });
});

$route->group('/tarefas', function(RouteCollectorProxyInterface $group) {
  $group->get('/cadastrar', function(Request $request, Response $response) {
    return (new CadastroTarefa($response))->view()->getRenderResponse();
  });
  
  $group->get('/ver[/{id}]', function(Request $request, Response $response, array $arguments) {
    return (new EditarTarefa($response))->view()->getRenderResponse();
  });
});
// ROTAS DO SISTEMA
