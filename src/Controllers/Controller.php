<?php

namespace AgendaPonto\Controllers;

use AgendaPonto\Models\DTOs\ModeloDTO;
use Slim\Psr7\Response;

abstract class Controller {
  protected $status;
  protected $response;
  protected $pathView;

  protected array $dataOthers = [];
  
  private $redirectTo;
  private $slimrResponse;

  public function __construct(Response $response) {
    $this->slimrResponse = $response;
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
      'response' => $this->response,
      'status'   => $this->status
    ];

    $this->slimrResponse->getBody()->write(twig($this->pathView, array_merge($defaultData, $dataObDTO, $this->dataOthers)));
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

  protected function getResponse() {
    return $this->response;
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
}