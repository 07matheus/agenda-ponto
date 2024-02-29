<?php

namespace AgendaPonto\Controllers;

use AgendaPonto\Configs\Resources;
use AgendaPonto\Models\DTOs\ModeloDTO;
use Slim\Psr7\{Request, Response};

abstract class Controller {
  protected $status;
  protected $response;
  protected $pathView;

  protected array $dataOthers = [];
  protected array $requestParams = [];
  
  private $redirectTo;
  private $slimrResponse;
  private $slimRequest;

  private array $resourcesFilesCSS = [];
  private array $resourcesFilesJS = [];

  public function __construct(Response $response, Request $request = null) {
    $this->slimrResponse = $response;
    $this->slimRequest   = $request;
  }

  abstract protected function validarTamanhoCampo($campo, $valorCampo): void;

  protected function validarDadosFormulario($obDto): void {
    $properties   = array_keys($obDto->getProperties());
    $this->status = true;

    foreach($properties as $campo) {
      $valor = $obDto->{$campo};

      $this->validarTamanhoCampo($campo, $valor);

      if(!$this->status) break;
    }
  }

  public function getRenderResponse(ModeloDTO $obDataDTO = null) {
    // ADICIONA O REDIRECT
    if($this->status && !is_null($this->redirectTo)) {
      header("Location: " . $this->redirectTo);
      exit;
    }

    $dataObDTO   = ($obDataDTO instanceof ModeloDTO) ? $obDataDTO->toArray(): [];
    $defaultData = [
      'response'          => $this->response,
      'status'            => $this->status,
      'resourcesFilesCSS' => $this->resourcesFilesCSS,
      'resourcesFilesJS'  => $this->resourcesFilesJS,
      'pathResourcesImg'  => $_ENV['APP_CAMINHO'] . '/resources/imgs'
    ];

    $dadosLayout = array_merge($defaultData, $dataObDTO, $this->dataOthers);
    $this->slimrResponse->getBody()->write(twig($this->pathView, $dadosLayout));
    return $this->slimrResponse;
  }

  protected function setFromToRedirect(string $to): Controller {
    $this->redirectTo = $to;

    return $this;
  }

  protected function setPathView(string $pathView): Controller {
    $this->pathView = $pathView;
    return $this;
  }

  public function setRequestParams(array $params = []): Controller {
    $this->requestParams = $params;
    return $this;
  }

  protected function getResponse() {
    return $this->response;
  }

  protected function getSlimRequest(): Request {
    return $this->slimRequest;
  }

  public function save($obDto): Controller {
    return $this;
  }

  public function update($obDto): Controller {
    return $this;
  }

  public function delete($obDto): Controller {
    return $this;
  }

  public function view(): Controller {
    return $this;
  }

  public function viewAll(): Controller {
    return $this;
  }

  protected function getResourcesFilesCompiled(string $tipo, string $diretorio): Controller {
    if(!in_array($tipo, ['js', 'css']) || !strlen($diretorio)) return '';

    $arquivos = Resources::getFullPathResources("$tipo/$diretorio");
    switch($tipo) {
      case 'css':
        $this->resourcesFilesCSS += $arquivos;
      break;

      case 'js':
        $this->resourcesFilesCSS += $arquivos;
      break;
    }

    return $this;
  }
}