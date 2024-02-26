<?php

namespace AgendaPonto\Configs;

use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class App {
  /**
   * Engine para renderização dos layouts
   * @var Environment
   */
  private static $twig;

  // GETTERS
  /**
   * Método responsável por retornar o template engine
   * @return Environment
   */
  public static function getEngineTemplate(): Environment {
    return self::$twig;
  }

  // GETTERS
  
  /**
   * Método responsável por iniciar as configurações do projeto
   * @return void
   */
  public static function init(): void {
    self::setConstantes();
    self::getEnvProject();
    self::defineDiretoriesApplication();
    self::setEngineTemplates();
    self::setConnectionDatabase();
  }

  /**
   * Método responsável por definir as constantes do projeto
   * @return void
   */
  private static function setConstantes(): void {
    if(!defined('ROOT')) define('ROOT', __DIR__ . '/../..');
    if(!defined('CACHE')) define('CACHE', ROOT . '/cache');
    if(!defined('LOGS')) define('LOGS', ROOT . '/logs');

    if(!defined('SRC')) define('SRC', ROOT . '/src');
    if(!defined('VIEW')) define('VIEW', SRC . '/Views');
    if(!defined('ROUTES')) define('ROUTES', SRC . '/Routes');
    
    if(!defined('RESOURCES')) define('RESOURCES', ROOT . '/resources');
  }

  /**
   * Método responsável por carregar o arquio de configuração
   * @return void
   */
  private static function getEnvProject(): void {
    $obDotEnv = Dotenv::createMutable(ROOT);
    $obDotEnv->load();
  }

  /**
   * Método responsável por retornar os diretórios que o projeto dever possuir
   * @return array
   */
  private static function getDiretoriesApplication(): array {
    return [
      CACHE,
      CACHE . '/layouts',
      RESOURCES,
      RESOURCES . '/css',
      RESOURCES . '/js',
      RESOURCES . '/imgs'
    ];
  }

  /**
   * Método responsável por criar os diretórios do projeto
   * @return void
   */
  private static function defineDiretoriesApplication(): void {
    $permission = (int) ($_ENV['APP_PERMISSION_DIRECTORIES'] ?? 0);
    if($permission  <= 0) return;

    foreach(self::getDiretoriesApplication() as $dir) {
      if(is_dir($dir)) continue;

      shell_exec("mkdir -p $dir");
      shell_exec("chmod -R $permission");
    }
  }

  /**
   * Método responsável por instanciar a engine de templates
   * @return void
   */
  private static function setEngineTemplates(): void {
    $loader     = new FilesystemLoader(VIEW);
    self::$twig = new Environment($loader, ['cache' => CACHE . '/layouts']);
  }

  /**
   * Método responsável por configurar a conexão com o banco de dados
   * @return void
   */
  private static function setConnectionDatabase(): void {
    $capsule = new Capsule;

    $capsule->addConnection([
      'driver'    => $_ENV['DB_DRIVER'] ?? null,
      'host'      => $_ENV['DB_HOST'] ?? null,
      'database'  => $_ENV['DB_NAME'] ?? null,
      'username'  => $_ENV['DB_USER'] ?? null,
      'password'  => $_ENV['DB_PASS'] ?? null,
      'charset'   => $_ENV['DB_CHARSET'] ?? null,
      'collation' => $_ENV['DB_COLLATION'] ?? null,
      'prefix'    => $_ENV['DB_PREFIX'] ?? null,
    ]);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
  }

  /**
   * Método responsável por renderizar o projeto
   * @return void
   */
  public static function show(): void {
    try {
      $route = AppFactory::create();
  
      require_once ROUTES . '/web.php';
      
      $route->run();
    } catch(HttpNotFoundException $ex) {
      header("HTTP/1.0 404 Not Found");
      echo twig('paginas/erros/404', [
        'debugOn' => ($_ENV['APP_DEBUG'] === 'true'),
        'traces'  => $ex->getTrace()
      ]);
    }
  }
}