<?php
/*
use Psr\Container\ContainerInterface;
return function (ContainerInterface $container) {
  $container->set('settings', function() {
    return [
      'displayErrorDetails' => true,
      'logErrorDetails' => true,
      'logErrors' => true,
    ];
  });
};
*/

$config = file_exists('../.env') ? parse_ini_file('../.env') : [];

$rootdir = dirname(__DIR__);
$publicdir = constant('DOCUMENT_ROOT_DIR') ?? 'www';
$tmpdir = "$rootdir/tmp";

$settings = [
  'root' => $rootdir,
  'temp' => $tmpdir,
  'public' => "$rootdir/$publicdir",
  'media' => "$rootdir/cdn",
  'indexFile' => 'index.html',
  'appUrl' => $config['APP_URL'] ?? 'http://localhost:8000',
  'publicDir' => $config['PUBLIC_DIR'] ?? '/app/www',
  'appName' => $config['APP_NAME'] ?? 'SlimApp',
  // Error Handling Middleware settings
  'error' => [
    // Should be set to false in production
    'display_error_details' => true,
    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => true,
    // Display error details in error log
    'log_error_details' => true
  ],
  // Sessions support: https://www.php.net/manual/en/session.configuration.php
  'session' => [
    'name' => 'ISSID',
    'cache_expire' => 0,
    'save_path' => $tmpdir
  ],
  // Database settings
  'db' => [
    'driver' => 'mysql',
    'host' => $config['DB_HOST'] ?? 'db',
    'port' => $config['DB_PORT'] ?? 3306,
    'username' => $config['DB_USERNAME'] ?? 'php_app',
    'database' => $config['DB_DATABASE'] ?? 'php_app',
    'password' => $config['DB_PASSWORD'] ?? 'password',
    'charset' => 'utf8',
    #'collation' => 'utf8mb4_unicode_ci',
    'flags' => [
      // Turn off persistent connections
      PDO::ATTR_PERSISTENT => false,
      // Enable exceptions
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      // Emulate prepared statements
      PDO::ATTR_EMULATE_PREPARES => true,
      // Set default fetch mode to array
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      // Set character set
      PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8 COLLATE utf8_unicode_ci'
    ]
  ],
  'auth' => [
    [
      'login' => 'test',
      'password' => 'test',
      'role' => 'tester'
    ],
    [
      'login' => 'root',
      'password' => 'root',
      'role' => 'admin'
    ]
  ]
];

return $settings;
