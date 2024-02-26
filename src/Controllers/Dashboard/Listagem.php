<?php

namespace AgendaPonto\Controllers\Dashboard;

use AgendaPonto\Configs\Session;
use AgendaPonto\Controllers\Controller;
use AgendaPonto\Models\DTOs\TarefaDTO;
use AgendaPonto\Models\Model\Tarefa;

class Listagem extends Controller {
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    return;
  }

  public function viewAll(): Controller {
    $this->setPathView('paginas/dashboard/listagem');
    $usuarioSessao = (new Session)->get(['usuario']);

    // BUSCA AS TAREFAS DE UM USUÁRIO
    $tarefas = Tarefa::where(
      'id_usuario', '=', (int) $usuarioSessao['id']
    )->get();

    // PREPARA OS DADOS DO LAYOUT
    $dadosTarefas = [];
    foreach($tarefas as $obTarefa) {
      $obTarefaDTO    = (new TarefaDTO)->setDados($obTarefa->getAttributes());
      $dadosTarefas[] = $obTarefaDTO->toArray();
    }

    $this->dataOthers = [
      'nomeUsuario'  => $usuarioSessao['nome'],
      'dadosTarefas' => $dadosTarefas
    ];

    return $this;
  }
}