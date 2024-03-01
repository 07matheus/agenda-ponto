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

  private function defineResourcesLayout(): void {
    $this->setPathView('paginas/usuario/editar');
    $this->getResourcesFilesCompiled('css', 'geral');
    $this->getResourcesFilesCompiled('js', 'geral');
    $this->getResourcesFilesCompiled('css', 'editar-tarefa');
    $this->getResourcesFilesCompiled('css', 'editar-usuario');
    $this->getResourcesFilesCompiled('js', 'editar-usuario');
  }

  public function view(): Editar {
    $this->defineResourcesLayout();

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

  public function update($obUsuarioDTO): Editar {
    // DADOS DE EXIBIÇÃO DA PÁGINA
    $this->defineResourcesLayout();
    $this->dataOthers = [
      'local'                  => 'editar',
      'tituloEspecificoPagina' => 'Editar'
    ];

    // VERIFICA SE O USUÁRIO ESTÁ LOGADO
    $obSessao = new Session;
    $usuario  = $obSessao->get(['usuario']);
    if(!(isset($usuario['id']) && is_numeric($usuario['id']))) {
      throw new HttpNotFoundException(
        $this->getSlimRequest(),
        'Você não tem permissão para realizar essa ação'
      );
    }

    // VALIDAÇÕES DOS CAMPOS ENVIADOS PELO USUÁRIO
    $this->validarTamanhoCampo('email', $obUsuarioDTO->email ?? '');
    $this->validarTamanhoCampo('nome', $obUsuarioDTO->nome ?? '');

    // VERIFICA SE IRÁ VALIDAR A SENHA
    $alterarSenha = $obUsuarioDTO->alterarSenha == 's';
    if($alterarSenha) {
      $this->validarTamanhoCampo('senha', $obUsuarioDTO->senha ?? '');
      $obUsuarioDTO->set('senha', md5($obUsuarioDTO->senha));
    }

    // REALIZA A ATUALIZAÇÃO DOS DADOS DO USUÁRIO
    if($this->status) {
      $dadosAtualizar = [
        'email' => $obUsuarioDTO->email,
        'nome'  => $obUsuarioDTO->nome
      ];

      if($alterarSenha) $dadosAtualizar['senha'] = $obUsuarioDTO->senha;
      
      // SALVA OS DADOS
      $sucesso         = (Usuario::where('id', $usuario['id'])->update($dadosAtualizar) > 0);
      $mensagemSucesso = 'Dados alterados com sucesso!';
      $mensagemErro    = 'Não foi possível atualizar os seus dados. Tente novamente mais tarde.';

      // ALTERA OS DADOS DE RESPOSTA
      $this->status = $sucesso;
      $this->response = $sucesso ? $mensagemSucesso: $mensagemErro;

      // ATUALIZA OS DADOS NA SESSÃO
      if($sucesso) $obSessao->set(['usuario'], [
        'id'    => $usuario['id'],
        'email' => $obUsuarioDTO->email,
        'nome'  => $obUsuarioDTO->nome
      ]);
    }

    return $this;
  }
}