<?php
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use Odan\Session\PhpSession;
use Odan\Session\SessionInterface;
use Odan\Session\Middleware\SessionMiddleware;
use Psr\Http\Message\ResponseFactoryInterface;
// used in case site hosted from directory (not from root)
// use Selective\BasePath\BasePathMiddleware;
use Psr\Container\ContainerInterface;

return [

  'settings' => function () {
    return require __DIR__ . '/settings.php';
  },

  SessionInterface::class => function (ContainerInterface $container) {
    $settings = $container->get('settings');
    $session = new PhpSession();
    $session->setOptions((array)$settings['session']);
    return $session;
  },

  SessionMiddleware::class => function (ContainerInterface $container) {
    return new SessionMiddleware($container->get(SessionInterface::class));
  },

  App::class => function (ContainerInterface $container) {
    AppFactory::setContainer($container);
    return AppFactory::create();
  },

  ErrorMiddleware::class => function (ContainerInterface $container) {
    $app = $container->get(App::class);
    $settings = $container->get('settings')['error'];
    return new ErrorMiddleware(
      $app->getCallableResolver(),
      $app->getResponseFactory(),
      (bool)$settings['display_error_details'],
      (bool)$settings['log_errors'],
      (bool)$settings['log_error_details']
    );
  },

  PDO::class => function (ContainerInterface $container) {
    $settings = $container->get('settings')['db'];
    $host = $settings['host'];
    $port = $settings['port'] ?? '3306';
    $dbname = $settings['database'];
    $username = $settings['username'];
    $password = $settings['password'];
    $charset = $settings['charset'];
    $flags = $settings['flags'];
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
    return new PDO($dsn, $username, $password, $flags);
  },

  // used in case site hosted from directory (not from root)
  /*BasePathMiddleware::class => function (ContainerInterface $container) {
    return new BasePathMiddleware($container->get(App::class));
  },*/

  ResponseFactoryInterface::class => function (ContainerInterface $container) {
    return $container->get(App::class)->getResponseFactory();
  }
];
