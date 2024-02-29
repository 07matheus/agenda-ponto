<?php

namespace AgendaPonto\Configs;

use DirectoryIterator;

class Resources {
  /**
   * Método responsável por retornar os arquivos de recursos
   * @param  string       $caminho       Caminho até o diretório dos recursos
   * @return array
   */
  public static function getFullPathResources(string $caminho): array {
    $recursos = [];
    $caminho  = RESOURCES . '/' . $caminho;
    if(!is_dir($caminho)) return $recursos;

    $caminhoSistema = $_ENV['APP_CAMINHO'];
    foreach(new DirectoryIterator($caminho) as $file) {
      if($file->isDot()) continue;

      $caminhoCompleto = $file->getPathname();
      $caminhoAlterado = str_replace(ROOT, '', $caminhoCompleto);
      $recursos[]      = $caminhoSistema . $caminhoAlterado;
    }
    
    return $recursos;
  }
}