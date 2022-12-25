<?php
namespace App\Actions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Odan\Session\SessionInterface;
use App\Models\TestModel;

final class TaskAction {
  /**
   * @var SessionInterface
   */
  private $session;
  private $modelTest;
  public function __construct(
    TestModel $modelTest,
    SessionInterface $session
  ) {
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
      $data['errors']  = $errors;
    }
    $payload = json_encode($data);
    $res->getBody()->write($payload);
    return $res
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(201);
  }
}
