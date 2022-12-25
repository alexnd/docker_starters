<?php
/*
  Router map
  The request object also has bulk functions as well.
  $request->getAttributes() and $request->withAttributes()
*/
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
// use Slim\Exception\HttpNotFoundException;
use Odan\Session\Middleware\SessionMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
// use Jenssegers\Blade\Blade;
// use DI\Bridge\Slim\ControllerInvoker;
use App\Middleware\AuthMiddleware;

return function(App $app) {

  // Allow CORS preflight requests for /
  $app->options('/{routes:.+}', function (
    ServerRequestInterface $request,
    ResponseInterface $response
  ): ResponseInterface {
    return $response;
  });

  // responce index.html
  $app->get('[/]', \App\Actions\BlogAction::class . ':index');
  # render blade view, we can pass session initdata immediately in HTML DOM!
  #$app->get('/', function (ServerRequestInterface $req, ResponseInterface $res) {
  #  return view($res, 'home', [
  #    'test' => time()
  #  ]);
  #});

  $app->get('/login[/]', \App\Actions\LoginAction::class . ':loginForm')->setName('login');

  $app->get('/maintenance[/]', \App\Actions\IndexAction::class . ':indexHTML');

  $app->get('/about[/]', \App\Actions\PagesAction::class . ':aboutHTML');

  $app->get('/faq[/]', \App\Actions\PagesAction::class . ':faqHTML');

  $app->get('/terms[/]', \App\Actions\PagesAction::class . ':termsHTML');

  $app->post('/login', \App\Actions\LoginAction::class);

  $app->map(['GET', 'POST'], '/logout', \App\Actions\LogoutAction::class)->setName('logout');

  $app->get('/post[/]', \App\Actions\BlogAction::class . ':postForm')->setName('create_post');
  $app->post('/post[/]', \App\Actions\BlogAction::class . ':post')->setName('submit_post');

  # json rest api. authorization restriction
  $app->group('/api', function (RouteCollectorProxy $group) {

    # /api
    $group->get('[/]', \App\Actions\IndexAction::class);

    # /api/login
    $group->post('/login[/]', \App\Actions\LoginAction::class);

    # /api/logout
    $group->map(['GET', 'POST'], '/logout[/]', \App\Actions\LogoutAction::class);

    # /api/customers
    $group->get('/customers[/]', \App\Actions\CustomerAction::class)
      ->add(AuthMiddleware::class);

    # /api/customer/<id>
    $group->get('/customer/{id:[0-9]+}[/]', \App\Actions\CustomerAction::class . ':getCustomer')
      ->add(AuthMiddleware::class);

    # /api/works
    // $group->get('/works/{name}', \App\Actions\WorkAction::class . ':getForExecutor');

    # /api/nextorderid
    $group->get('/nextorderid[/]', \App\Actions\OrderAction::class . ':getNextOrderId')
        ->add(AuthMiddleware::class);

    # /api/orders
    $group->get('/orders[/]', \App\Actions\OrderAction::class );

    # /api/order
    $group->get('/order/{id:[0-9]+}[/]', \App\Actions\OrderAction::class . ':getOrder');
    $group->post('/order[/]', \App\Actions\OrderAction::class . ':createOrder');

    # /api/task
    $group->post('/task[/]', \App\Actions\TaskAction::class . ':createTask');

    # /api/subjects
    //$group->get('/subjects/{name}', \App\Actions\SubjectAction::class . ':getForExecutor');

    # /api/findcustomer/<query>
    $group->get('/findcustomer/{q}', \App\Actions\CustomerAction::class . ':find');
  })
    ->add(\App\Middleware\CorsMiddleware::class);

  $app->get('/test[/]', \App\Actions\IndexAction::class . ':testAction')->setName('test');

  #$app->map(['GET','POST'], '/task', \App\Actions\TaskAction::class . ':test');

  // Password protected area, for debug
  $app->group('/cabinet', function (RouteCollectorProxy $group) {
    $group->get('[/]', function (ServerRequestInterface $req, ResponseInterface $res) {
      return view($res, 'cabinet', [
        'user' => session('user', 'unknown'),
        'role' => session('role', 'guest'),
        'csrf_token' => csrf_token()
      ]);
    })->setName('cabinet');
    $group->get('/stuff/{id:[0-9]+}', function (ServerRequestInterface $req, ResponseInterface $res, array $args) {
      $res->getBody()->write(
        dump($args, true)
      );
      return $res;
    });
  })->add(AuthMiddleware::class);

  $app->get('/hph.info', function (ServerRequestInterface $req, ResponseInterface $res) {
    phpinfo();
    return $res;
  });

  # examples

  //$app->get('/', \HomeController::class . ':home');
  /*
    $app->get('/foo[/{foo}]', function (ServerRequestInterface $req, ResponseInterface $res, array $args) {
    $foo = $args['foo'];
    $res->getBody()->write(
      empty($foo)
      ? "Foo unknown"
      : "<h3>Glory to $foo!</h3>"
    );
    return $res;
  });
  */
  /*
  $app->get('/test', function (ServerRequestInterface $req, ResponseInterface $res) {
    $c = setting('auth');
    $res->getBody()->write(
      dump($c, true)
    );
    return $res;
  });
  */

  # CORS
  /*$app->options('/{routes:.+}', function (ServerRequestInterface $req, ResponseInterface $res, array $args) {
    return $response;
  });
  $app->add(function (ServerRequestInterface $req, ResponseInterface $res, $next) {
    $response = $next->handle($req);
    return $response
      ->withHeader('Access-Control-Allow-Origin', '*')
      ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
      ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
  });*/

  # 404 page
  $app->map(
    ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}',
    function (ServerRequestInterface $req, ResponseInterface $res) {
      // throw new HttpNotFoundException($req);
      $uri = $req->getUri()->getPath();
      if (is_xhr_req($req)) {
        return res_json($res, ['error' => 'Not Found'], 404);
      } else {
        $res->getBody()->write(
          "<h1>Error 404</h1><p>Page <b>$uri</b> not exists</p>"
        );
      }
      return $res->withStatus(404);
    }
  );
};
