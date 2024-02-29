<?php

namespace AgendaPonto\Controllers;

class Inicio extends Controller {
  protected function validarTamanhoCampo($campo, $valorCampo): void {
    return;
  }

  public function view(): Inicio {
    $this->setPathView('paginas/inicio');
    $this->getResourcesFilesCompiled('css', 'inicio');

    return $this;
  }
}