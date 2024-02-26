<?php

namespace AgendaPonto\Controllers\Tarefas;

use AgendaPonto\Controllers\Controller;

class Cadastro extends Controller {
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    return;
  }

  public function view(): Cadastro {
    $this->setPathView('paginas/tarefas/cadastro');

    $this->dataOthers = [
      'isAtualizacao'       => false,
      'tituloFormulario'    => 'Cadastrar tarefa',
      'nomeBotaoFormulario' => 'Criar'
    ];

    return $this;
  }
}