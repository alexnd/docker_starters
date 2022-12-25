<?php

namespace App\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Odan\Session\SessionInterface;
use App\Models\TestModel;

final class IndexAction
{
    /**
     * @var SessionInterface
     */
    private $session;
    private $modelTest;

    public function __construct(
        TestModel $modelTest,
        SessionInterface $session
    )
    {
        $this->modelTest = $modelTest;
        $this->session = $session;
    }

    /**
     * Invoke.
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        #if (is_xhr_req($req)) {
        #    return $this->indexHTML($req, $res);
        #} else {
        $data = [
            'version' => '1.0',
            'name' => 'AppName'
        ];
        $data['role'] = session('role', 'guest');
        $data['user'] = session('user', '');
        $data['csrf_token'] = csrf_token();
        return res_json($res, $data, 200);
    }

    public function indexHTML(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $filename = setting('indexFile', 'index.html');
        $path = public_path($filename);
        $res
            ->getBody()
            ->write(file_exists($path) ? file_get_contents($path) : "Not Exist: $filename");
        return $res;
    }

  public function index(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
  {
    $data = [
      'title' => "Happy DJ's Base",
      'role' => session('role', 'guest'),
      'user' => session('user', ''),
      'csrf_token' => csrf_token(),
    ];
    return view($res, 'home', $data);
  }

  public function test(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
  {
    $errors = [];
    $data = [];
    $params = (array)$req->getParsedBody();
    if (empty($params['name'])) $errors['name'] = 'Name is required.';
    if (empty($params['superheroAlias'])) $errors['superheroAlias'] = 'Superhero alias is required.';
    if (empty($errors)) {
      $data['success'] = true;
      $data['message'] = 'Success!';
      if ($params['superheroAlias'] === 'ND') {
        $data['data'] = $this->modelTest->test();
      }
    } else {
      $data['success'] = false;
      $data['errors'] = $errors;
    }
    $payload = json_encode($data);
    $res->getBody()->write($payload);
    return $res
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(201);
  }

  public function testAction(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
  {
    $data = [
      'title' => "Happy DJ's Base",
      'role' => session('role', 'guest'),
      'user' => session('user', ''),
      'csrf_token' => csrf_token(),
    ];
    return view($res, 'test', $data);
  }

}
