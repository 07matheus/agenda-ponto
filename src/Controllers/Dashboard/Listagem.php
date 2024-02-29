<?php

namespace AgendaPonto\Controllers\Dashboard;

use AgendaPonto\Configs\Session;
use AgendaPonto\Controllers\Controller;
use AgendaPonto\Models\DTOs\TarefaDTO;
use AgendaPonto\Models\Model\Tarefa;
use DateTime;

class Listagem extends Controller {
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    return;
  }

  public function viewAll(): Controller {
    $this->setPathView('paginas/dashboard/listagem');
    $this->getResourcesFilesCompiled('js', 'geral');
    $this->getResourcesFilesCompiled('css', 'geral');
    $this->getResourcesFilesCompiled('css', 'dashboard');

    $usuarioSessao = (new Session)->get(['usuario']);

    // BUSCA AS TAREFAS DE UM USUÁRIO
    $tarefas = Tarefa::where(
      'id_usuario', '=', (int) $usuarioSessao['id']
    )->get();

    // PREPARA OS DADOS DO LAYOUT
    $dadosTarefas = [];
    foreach($tarefas as $key => $obTarefa) {
      $obTarefaDTO = (new TarefaDTO)->setDados($obTarefa->getAttributes(), false);
      $dadosTarefa = $obTarefaDTO->toArray();

      // FORMATAÇÃO DOS DADOS
      $dadosTarefa['dataVencimento'] = (new DateTime($dadosTarefa['dataVencimento']))->format('d/m/Y');
      $dadosTarefa['tipoLayout']     = (is_int($key / 2)) ? '_01': '_02';
      $dadosTarefa['prioridade']     = ucfirst($dadosTarefa['prioridade']);
      
      // SALVA OS DADOS DO LAYOUT
      $dadosTarefas[] = $dadosTarefa;
    }

    $this->dataOthers = [
      'nomeUsuario'  => $usuarioSessao['nome'],
      'dadosTarefas' => $dadosTarefas
    ];

    return $this;
  }
}