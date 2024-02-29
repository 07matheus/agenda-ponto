<?php

use AgendaPonto\Configs\Session;
use AgendaPonto\Controllers\Dashboard\Listagem as MinhasTarefas;
use AgendaPonto\Controllers\Usuario\Cadastro as CadastroUsuario;
use AgendaPonto\Controllers\Usuario\Editar as EditarUsuario;
use AgendaPonto\Controllers\Usuario\Login as LoginUsuario;
use AgendaPonto\Controllers\Tarefas\Cadastro as CadastroTarefa;
use AgendaPonto\Controllers\Tarefas\Editar as EditarTarefa;
use AgendaPonto\Middlewares\{RequiredLoginMiddleware, ForceLoginMiddleware};
use AgendaPonto\Models\DTOs\TarefaDTO;
use AgendaPonto\Models\DTOs\UsuarioDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;

$request  = Request::class;
$response = Request::class;

// ROTAS DO SISTEMA
$route->get('/', function(Request $request, Response $response) {
  header('Location: /entrar');
  exit;
})->add(new ForceLoginMiddleware);

$route->get('/entrar', function(Request $request, Response $response) {
  return (new LoginUsuario($response))->view()->getRenderResponse();
})->add(new ForceLoginMiddleware);

$route->get('/usuario/cadastro', function(Request $request, Response $response) {
  return (new CadastroUsuario($response))->view()->getRenderResponse(new UsuarioDTO);
})->add(new ForceLoginMiddleware);

$route->post('/usuario/cadastro', function(Request $request, Response $response) {
  $dtoUsuario   = (new UsuarioDTO)->setDados($request->getParsedBody());
  $obController = new CadastroUsuario($response);

  return $obController->save($dtoUsuario)->getRenderResponse($dtoUsuario);
})->add(new ForceLoginMiddleware);

$route->get('/dashboard', function(Request $request, Response $response) {
  return (new MinhasTarefas($response))->viewAll()->getRenderResponse();
})->add(new RequiredLoginMiddleware);

$route->post('/entrar', function(Request $request, Response $response) {
  $dtoUsuario   = (new UsuarioDTO)->setDados($request->getParsedBody());
  $obController = new LoginUsuario($response);

  return $obController->save($dtoUsuario)->getRenderResponse();
})->add(new ForceLoginMiddleware);

$route->group('/usuario', function(RouteCollectorProxyInterface $group) {
  $group->get('/editar', function(Request $request, Response $response) {
    $obController = new EditarUsuario($response, $request);
    
    return $obController->view()->getRenderResponse();
  });

  $group->get('/sair', function(Request $request, Response $response) {
    (new Session)->cleanSession(['usuario']);

    header("Location: /");
    exit;
  });
  
  $group->post('/editar', function(Request $request, Response $response) {
    $obUsuarioDTO = (new UsuarioDTO)->setDados($request->getParsedBody(), true, true);
    $obController = new EditarUsuario($response);

    return $obController->update($obUsuarioDTO)->getRenderResponse($obUsuarioDTO);
  });
})->add(new RequiredLoginMiddleware);

$route->group('/tarefas', function(RouteCollectorProxyInterface $group) {
  $group->get('/cadastrar', function(Request $request, Response $response) {
    return (new CadastroTarefa($response))->view()->getRenderResponse();
  });

  $group->post('/cadastrar', function(Request $request, Response $response) {
    $obTarefaDTO  = (new TarefaDTO)->setDados($request->getParsedBody());
    $obController = new CadastroTarefa($response);

    return $obController->save($obTarefaDTO)->getRenderResponse();
  });
  
  $group->get('/ver[/{id}]', function(Request $request, Response $response, array $arguments) {
    if(!is_numeric(($arguments['id'] ?? null))) {
      throw new HttpNotFoundException($request, "Tarefa não encontrada");
    }

    $obController = new EditarTarefa($response, $request);
    $obController->setRequestParams($arguments);

    return $obController->view()->getRenderResponse();
  });

  $group->post('/ver[/{id}]', function(Request $request, Response $response, array $arguments) {
    $obTarefaDTO  = (new TarefaDTO)->setDados($request->getParsedBody());
    $obController = new EditarTarefa($response, $request);

    // ADICIONA O ID DA TAREFA
    $obTarefaDTO->set('id', ($arguments['id'] ?? null));

    return $obController->update($obTarefaDTO)->getRenderResponse();
  });
})->add(new RequiredLoginMiddleware);
// ROTAS DO SISTEMA
