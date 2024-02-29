<?php

namespace AgendaPonto\Controllers\Usuario;

use AgendaPonto\Configs\Session;
use AgendaPonto\Models\Model\Usuario;
use AgendaPonto\Controllers\Controller;

class Login extends Controller {
  /**
   * Método responsável por validar a quantidade máxima e mínima dos campos de um formulário de usuário
   * @param  string       $campo            Campo do formulário
   * @param  string       $valorCampo       Valor que foi recebido no campo
   * @return void
   */
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    $quantidadeMaxima = null;
    $quantidadeMinima = null;
    $this->status     = true;

    switch($campo) {
      case 'email':
        $quantidadeMaxima = 255;
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

  /**
   * Mostra o formulário de login
   * @return Login
   */
  public function view(): Login {
    $this->setPathView('paginas/usuario/login');
    $this->getResourcesFilesCompiled('css', 'geral');
    $this->getResourcesFilesCompiled('css', 'login');
    $this->getResourcesFilesCompiled('js', 'geral');
    return $this;
  }

  /**
   * Verifica se o usuário pode realizar login no sistema
   * @param  UsuarioDTO $obDto
   * @return Login
   */
  public function save($obUsuarioDTO): Login {
    // DEFINIÇÃO DO LAYOUT
    $this->setPathView('paginas/usuario/login');
    $this->getResourcesFilesCompiled('css', 'geral');
    $this->getResourcesFilesCompiled('css', 'login');
    $this->getResourcesFilesCompiled('js', 'geral');

    // VALIDAÇÃO DO FORMULÁRIO
    $this->validarDadosFormulario($obUsuarioDTO);
    if(!$this->status) return $this;

    // ADICIONA CRIPTOGRAFIA
    $obUsuarioDTO->set('senha', md5($obUsuarioDTO->senha));

    // VERIFICA SE O USUÁRIO INFORMADO PUSSUI LOGIN
    $condicoes = [
      ['email', '=', $obUsuarioDTO->email],
      ['senha', '=', $obUsuarioDTO->senha]
    ];
    $obUsuario = Usuario::where($condicoes)->first();
    if(!$obUsuario instanceof Usuario) {
      $this->status   = false;
      $this->response = 'O usuário ou senha incorretos.';
      return $this;
    }

    // SALVA OS DADOS NA SESSÃO
    $this->setFromToRedirect('/dashboard', 200);
    (new Session)->set(['usuario'], [
      'id'    => $obUsuario->id,
      'email' => $obUsuario->email,
      'nome'  => $obUsuario->nome
    ]);

    return $this;
  }
}