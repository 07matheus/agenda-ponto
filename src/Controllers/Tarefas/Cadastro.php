<?php

namespace AgendaPonto\Controllers\Tarefas;

use AgendaPonto\Configs\Session;
use AgendaPonto\Controllers\Controller;
use AgendaPonto\Models\Model\Tarefa;

class Cadastro extends Controller {
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    $quantidadeMinima = null;
    $quantidadeMaxima = null;

    switch($campo) {
      case 'nome':
        $quantidadeMinima = 1;
        $quantidadeMaxima = 50;
      break;
      case 'prioridade':
        $opcoes = ['baixa', 'media', 'alta'];
        if(!in_array($valorCampo, $opcoes)) {
          $this->response = "As opções permitidas para o campo '$campo', são: " . implode(', ', $opcoes);
          $this->status   = false;
        }
        return;
      case 'dataVencimento':
        if(!(strlen($valorCampo) > 1 && strlen($valorCampo) <= 11)) {
          $this->response = 'O campo de data de vencimento não foi informado';
          $this->status   = false;
        }

        return;
      break;
    }

    if(is_numeric($quantidadeMinima) && is_numeric($quantidadeMaxima) && !is_null($valorCampo)) {
      $quantidade = strlen($valorCampo);
      if($quantidade < $quantidadeMinima || $quantidade > $quantidadeMaxima) {
        $this->response = "O campo '{$campo}' possui limitação de caracteres. Min.({$quantidadeMinima}) e Max.({$quantidadeMaxima})";
        $this->status   = false;
      }
    }
  }

  private function setDefinicoesDeLayout() {
    $this->setPathView('paginas/tarefas/cadastro');

    $this->dataOthers = [
      'isAtualizacao'       => false,
      'tituloFormulario'    => 'Cadastrar tarefa',
      'tituloPagina'        => 'Cadastro',
      'nomeBotaoFormulario' => 'Criar',
      'tipoFormulario'      => 'cadastrar'
    ];
  }

  public function view(): Cadastro {
    $this->setDefinicoesDeLayout();

    return $this;
  }

  public function save($obTarefaDTO): Cadastro {
    // VALIDAÇÃO DOS DADOS ENVIADOS
    $this->validarDadosFormulario($obTarefaDTO);

    // REALIZA O CADASTRO DA TAREFA
    $usuario = (new Session)->get(['usuario']);
    if($this->status) {
      $dadosCadastro = [
        'id_usuario'      => $usuario['id'],
        'nome'            => $obTarefaDTO->nome,
        'prioridade'      => $obTarefaDTO->prioridade,
        'descricao'       => $obTarefaDTO->descricao,
        'data_vencimento' => $obTarefaDTO->dataVencimento
      ];

      // SALVA OS DADOS
      $obNovaTarefa = Tarefa::create($dadosCadastro);
      $sucesso      = ($obNovaTarefa instanceof Tarefa);

      // MENSAGENS PARA RETORNO
      $this->status    = $sucesso;
      $mensagemErro    = 'Não foi possível criar a nova tarefa. Tente novamente mais tarde.';
      $mensagemSucesso = 'Tarefa criada com sucesso!';
      $this->response  = $sucesso ? $mensagemSucesso: $mensagemErro;
    }

    // DEFINIÇÕES DO LAYOUT
    $this->setDefinicoesDeLayout();

    return $this;
  }
}