<?php

namespace AgendaPonto\Configs;

class Session {
  /**
   * Define a sessão do cliente
   * @return Session
   */
  public function defineSessionClient(): Session {
    $sessionId = session_id();
    if(!isset($_SESSION[$sessionId])) {
      $_SESSION[$sessionId] = [];
    }

    return $this;
  }

  /**
   * Método responsável por recuperar a sessão DO cliente
   * @param  array      $indices      Índices da sessão
   * @return mixed
   */
  public function get(array $indices = []): mixed {
    $sessao = $_SESSION[session_id()];
    if(empty($indices) && empty($sessao)) return [];

    foreach($indices as $hash) {
      if(!isset($sessao[$hash])) {
        $sessao = null;
        break;
      }

      $sessao = $sessao[$hash];
    }

    return $sessao;
  }

  /**
   * Método responsável por setar os dados na sessão
   * @param  array      $index      Índide da sessão
   * @param  mixed      $value      Valor que será inserido
   * @return void
   */
  public function set(array $index = [], $value = null): void {
    $isNotNull = is_null($value);
    $isArray   = is_array($value);
    $isNumeric = is_numeric($value);
    $isString  = is_string($value);
    if(!($isNotNull || $isArray || $isNumeric || $isString)) return;

    $_SESSION[session_id()] = $this->iteratingNewSession($this->get(), $index, $value);
  }

  /**
   * Método responsável por realizar a iteração dos dados da sessão
   * @param  mixed      $session      Dados da sessão
   * @param  array      $indexes      Indices onde serão inseridos os dados
   * @param  mixed      $value        Dados que serão inseridos
   * @return array|mixed
   */
  private static function iteratingNewSession($session, $indexes, $value) {
    $hash = $indexes[0] ?? null;
    unset($indexes[0]);
    rsort($indexes);
    
    if(is_null($hash)) {
      return $value;
    }

    if(!isset($session[$hash])) $session[$hash] = null;

    $valor          = self::iteratingNewSession($session[$hash], $indexes, $value);
    $session[$hash] = $valor;
    return $session;
  }

  /**
   * Método responsável por remover uma sessão
   * @param  array      $indexes      Indices de onde os dados serão removidos
   * @return void
   */
  public function cleanSession(array $indexes) {
    if(empty($indexes)) return;

    $sessao = $this->get();
    $this->iteratingClearSession($indexes, $sessao);

    // ATUALIZA A SESSÃO
    $_SESSION[session_id()] = $sessao;
  }

  /**
   * Método responsável por iterar sobre todos os índices da sessão e remover o último
   * @param  array      $indexes      Indices de onde os dados serão removidos
   * @param  mixed      $session      Dados da sessão
   * @return void
   */
  private function iteratingClearSession(array $indexes, mixed &$session): void {
    $keyHash = array_key_first($indexes);
    if(is_null($keyHash)) {
      $session = null;
      return;
    }

    $hash = $indexes[$keyHash];
    unset($indexes[$keyHash]);
    rsort($indexes);

    $this->iteratingClearSession($indexes, $session[$hash]);

    if(is_null($session[$hash]) || (is_array($session[$hash]) && empty($session[$hash]))) unset($session[$hash]);
  }
}