<?php

namespace AgendaPonto\Controllers\Tarefas;

use AgendaPonto\Controllers\Controller;

class Editar extends Controller {
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    return;
  }

  public function view(): Editar {
    $this->setPathView('paginas/tarefas/cadastro');

    $this->dataOthers = [
      'isAtualizacao'       => true,
      'tituloFormulario'    => 'Editar tarefa',
      'nomeBotaoFormulario' => 'Salvar'
    ];

    return $this;
  }
}