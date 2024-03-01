<?php

namespace AgendaPonto\Controllers\Tarefas;

use AgendaPonto\Configs\Session;
use AgendaPonto\Controllers\Controller;
use AgendaPonto\Models\Model\Tarefa;

class Remover extends Controller {
  public function validarTamanhoCampo($campo, $valorCampo): void {
    return;
  }

  public function view(): Remover {
    $this->setPathView('paginas/tarefas/remover');
    $this->getResourcesFilesCompiled('css', 'geral');
    $this->getResourcesFilesCompiled('css', 'remover-tarefa');
    $this->getResourcesFilesCompiled('js', 'geral');
    $this->getResourcesFilesCompiled('js', 'remover-tarefa');

    return $this;
  }

  public function delete($obTarefaDTO): Remover {
    $obSessao   = new Session;
    $hashSessao = ['alerta', 'dashboard'];
    $usuario    = $obSessao->get(['usuario']);
    $location   = 'Location: /dashboard';
    
    // VERIFICA SE O ID DA TAREFA FOI ENVIADO
    if(!is_numeric($obTarefaDTO->id)) {
      $obSessao->set($hashSessao, [
        'sucesso'  => false,
        'mensagem' => 'Nenhuma tarefa foi selecionada para a remoção'
      ]);

      header($location);
      exit;
    }

    // VERIFICA SE A TAREFA PERTENCE AO USUÁRIO LOGADO
    $obTarefa = Tarefa::where([
      ['id', $obTarefaDTO->id],
      ['id_usuario', $usuario['id']]
    ])->first();
    if(!$obTarefa instanceof Tarefa) {
      $obSessao->set($hashSessao, [
        'sucesso'  => false,
        'mensagem' => 'Não foi possível realizar a remoção da tarefa'
      ]);

      header($location);
      exit;
    }

    // REMOVE A TAREFA
    if(!$obTarefa->delete()) {
      $obSessao->set($hashSessao, [
        'sucesso'  => false,
        'mensagem' => 'Não foi possível realizar a remoção da tarefa'
      ]);

      header($location);
      exit;
    }

    $obSessao->set($hashSessao, [
      'sucesso'  => true,
      'mensagem' => 'Tarefa removida com sucesso!'
    ]);

    header($location);
    exit;
  }
}