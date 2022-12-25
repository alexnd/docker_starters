<?php
use Slim\Middleware\ErrorMiddleware;
use Odan\Session\Middleware\SessionMiddleware;
// used in case site hosted from directory (not from root)
//use Selective\BasePath\BasePathMiddleware;
use Slim\App;

return function(App $app) {
  // Parse json, form data and xml
  $app->addBodyParsingMiddleware();
  // Start the session
  $app->add(SessionMiddleware::class);
  // Enable CORS
  $app->add(\App\Middleware\CorsMiddleware::class); 
  // Add the Slim built-in routing middleware
  $app->addRoutingMiddleware();
  //$app->add(BasePathMiddleware::class);
  // Catch exceptions and errors
  $app->add(ErrorMiddleware::class);
};
