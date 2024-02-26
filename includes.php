<?php

error_reporting(E_ALL);
ini_set('display_errors', true);

use AgendaPonto\Configs\{App, Cache, Session};

// INICIA A SESSÃO
session_start();

// ARQUIVOS DO SISTEMA
require_once __DIR__ . '/vendor/autoload.php';

// RECURSOS DO SISTEMA
App::init();
(new Session)->defineSessionClient();

// ENGINE DE TEMPLATE
function twig($name, $context = []) {
  $completeName = $name  . '.twig.html';
  if(!file_exists(VIEW . '/' . $completeName)) return '';

  $debugOn = (bool) ($_ENV['APP_DEBUG'] ?? 'false');
  if($debugOn) Cache::clearCache('layouts');

  return App::getEngineTemplate()->render($completeName, $context);
}