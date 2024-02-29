<?php

namespace AgendaPonto\Controllers\Usuario;

use AgendaPonto\Controllers\Controller;
use AgendaPonto\Models\DTOs\UsuarioDTO;
use AgendaPonto\Models\Model\Usuario;

class Cadastro extends Controller {
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

    if(is_null($valorCampo)) $valorCampo = '';

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

  /**
   * Realiza o cadastro do usuário
   * @param  UsuarioDTO       $obUsuarioDTO       Dados do usuaário
   * @return Cadastro
   */
  public function save($obUsuarioDTO): Cadastro {
    $this->setPathView('paginas/usuario/cadastro');
    $this->getResourcesFilesCompiled('css', 'geral');
    $this->getResourcesFilesCompiled('js', 'geral');
    $this->getResourcesFilesCompiled('css', 'cadastro');
    
    $this->validarDadosFormulario($obUsuarioDTO);

    // ADICIONA CRIPTOGRAFIA
    $obUsuarioDTO->set('senha', md5($obUsuarioDTO->senha));

    if($this->status) {
      // VERIFICA SE O USUÁRIO JÁ FOI CADASTRADO
      $obUsuario = Usuario::where('email', '=', "{$obUsuarioDTO->email}")->first();
      if($obUsuario instanceof Usuario) {
        $this->status   = false;
        $this->response = "O usuário informado já está cadastrado";
      }
  
      // CADASTRA O USUÁRIO NO BANCO
      if($this->status) {
        $obNovoUsuario = Usuario::create($obUsuarioDTO->toArray());
        $this->status  = ($obNovoUsuario instanceof Usuario);
      
        // DEFINE A PÁGINA DE CONFRMAÇÃO
        $this->setPathView('paginas/usuario/confirmacao');
        if(!$this->status) {
          $this->status   = false;
          $this->response = 'Não foi possível realizar o cadastro no momento. Tente novamente mais tarde!';
  
          // VOLTA PARA A PÁGINA DE CADASTRO
          $this->setPathView('paginas/usuario/cadastro');
        }
      }
    }

    $this->dataOthers = array_merge([
      'local'                  => 'cadastro',
      'tituloEspecificoPagina' => 'Cadastro'
    ], $obUsuarioDTO->toArray());

    return $this;
  }

  /**
   * Mostra o formulário de cadastro
   * @return Cadastro
   */
  public function view(): Cadastro {
    $this->setPathView('paginas/usuario/cadastro');
    $this->getResourcesFilesCompiled('css', 'geral');
    $this->getResourcesFilesCompiled('js', 'geral');
    $this->getResourcesFilesCompiled('css', 'cadastro');

    $this->dataOthers = [
      'local'                  => 'cadastro',
      'tituloEspecificoPagina' => 'Cadastro'
    ];
    return $this;
  }
}