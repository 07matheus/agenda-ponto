<?php

namespace AgendaPonto\Configs;

class Cache {
  /**
   * Método responsável por remover um cache
   * @param  string       $resource       Pasta do recurso que será removido
   * @return bool
   */
  public static function clearCache($resource = null): bool {
    $completePath = CACHE . "/$resource";
    if(!file_exists($completePath)) return false;

    shell_exec("rm -rf $completePath");
    return true;
  }
}