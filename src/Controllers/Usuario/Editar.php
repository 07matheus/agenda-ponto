<?php

namespace AgendaPonto\Controllers\Usuario;

use AgendaPonto\Configs\Session;
use AgendaPonto\Controllers\Controller;
use AgendaPonto\Models\DTOs\UsuarioDTO;
use AgendaPonto\Models\Model\Usuario;
use Slim\Exception\HttpNotFoundException;

class Editar extends Controller {
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    $quantidadeMaxima = null;
    $quantidadeMinima = null;
    $this->status     = true;

    switch($campo) {
      case 'email':
        $quantidadeMaxima = 255;
        $quantidadeMinima = 1;
      break;

      case 'nome':
        $quantidadeMaxima = 100;
        $quantidadeMinima = 1;
      break;

      case 'senha':
        $quantidadeMaxima = 10;
        $quantidadeMinima = 5;
      break;
    }

    if(is_numeric($quantidadeMinima) && is_numeric($quantidadeMaxima)) {
      $quantidade = strlen($valorCampo);
      if($quantidade < $quantidadeMinima || $quantidade > $quantidadeMaxima) {
        $this->response = "O campo '{$campo}' possui limitação de caracteres. Min.({$quantidadeMinima}) e Max.({$quantidadeMaxima})";
        $this->status   = false;
      }
    }
  }

  public function view(): Editar {
    $this->setPathView('paginas/usuario/cadastro');

    $usuario   = (new Session)->get(['usuario']);
    $idUsuario = $usuario['id'] ?? null;
    if(!is_numeric($idUsuario)) {
      throw new HttpNotFoundException(
        $this->getSlimRequest(),
        'Você não tem permissão para realizar essa ação'
      );
    }

    $this->dataOthers          = $usuario;
    $this->dataOthers['local'] = 'editar';

    return $this;
  }
}