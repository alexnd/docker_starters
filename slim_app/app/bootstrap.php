<?php
#declare(strict_types=1);
#error_reporting(0);
#ini_set('display_errors', '0');
date_default_timezone_set('Europe/Kiev');

use DI\ContainerBuilder;
use Slim\App;
require __DIR__ . '/../vendor/autoload.php';
//$settings = require __DIR__ . '/../app/settings.php';
//$routes = require __DIR__ . '/../app/routes.php';
//$middleware = require __DIR__ . '/../app/middleware.php';
/*$container = new Container;
$settings($container);
$app = SlimAppFactory::create($container);
$app->addRoutingMiddleware();
$_SERVER['app'] = &$app;
$middleware($app);
$routes($app);*/

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/container.php');
$container = $containerBuilder->build();
$app = $container->get(App::class);
$_SERVER['app'] = &$app;
(require __DIR__ . '/routes.php')($app);
(require __DIR__ . '/middleware.php')($app);
$app->run();
