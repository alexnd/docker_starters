<?php

namespace App\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Odan\Session\SessionInterface;
use App\Models\PostModel;

final class BlogAction
{
  /**
   * @var SessionInterface
   */
  private $session;
  private $modelPost;
  private $forms = [
    'post' => [
      'media' => 'file',
      'title' => 'str',
      'url' => 'str',
      'description' => 'str',
      'type' => 'num',
      'ispost' => 'bool'
    ]
  ];
  private $formKeys = [
    'file' => 'media'
  ];
  private $fileSizeLimit = 2097152; // 2MB
  private $allowedFileTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
  private $appTitle;

  public function __construct(
    PostModel        $modelPost,
    SessionInterface $session
  )
  {
    $this->modelPost = $modelPost;
    $this->session = $session;
    $this->appName = setting('appName', 'appName');
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

  public function index(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
  {
    $data = [
      'title' => $this->appName,
      'role' => session('role', 'guest'),
      'user' => session('user', ''),
      'csrf_token' => csrf_token(),
    ];
    return view($res, 'home', $data);
  }

  public function postForm(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
  {
    $data = [
      'title' => $this->appName,
      'role' => session('role', 'guest'),
      'user' => session('user', ''),
      'csrf_token' => csrf_token(),
    ];
    return view($res, 'post', $data);
  }

  public function post(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
  {
    $errors = [];
    $data = [
      'FILES' => $_FILES,
      'key' => $this->formKeys['file']
    ];
    $params = (array)$req->getParsedBody();
    $data['params'] = $params;
    $url = '/';
    if (isset($params['action']) && $_POST['action'] === 'Send') {
      $file = $_FILES[$this->formKeys['file']] ?? null;
      if ($file) {
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileType = $file['type'];
        $data['type'] = $fileType;
        if (
          $fileName
          && $fileSize < $this->fileSizeLimit
          && in_array($fileType, $this->allowedFileTypes)
        ) {
          $fileTmpPath = tmp_path($file['tmp_name']);
          $data['tmppath'] = $file['tmp_name'];
          if (is_uploaded_file($file['tmp_name'])) {
            $p = explode(".", $fileName);
            $fileExtension = strtolower(array_pop($p));
            $fileName = join('.', $p) . '.' . $fileExtension;
            $hashFileName = md5_file($file['tmp_name']) . '.' . $fileExtension;;
            $uploadPath = media_path($hashFileName);
            $data['uploadpath'] = $uploadPath;
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
              $data['filename'] = $fileName;
              $data['url'] = media_url($hashFileName);
            } else {
              $errors[] = "Upload write error";
            }
          } else {
            $errors[] = "Upload temp error";
          }
        } else {
          $errors[] = "Upload error";
        }
      } else {
        $data['noupload'] = true;
      }
      if (!count($errors)) {
        // TODO: RECORD DB
        if ($file) {
          // INSERT files SET ...
        }
      }
    } else {
      $errors['wrong_action'] = "Wrong action";
    }
    if (!empty($errors)) {
      $url = '/post';
    }
    $data['errors'] = $errors;
    dd($data);
    #return res_redirect($res, $url);
  }

  public function postAjax(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
  {
    $errors = [];
    $data = [];
    $params = (array)$req->getParsedBody();
    if (empty($params['file'])) $errors['file'] = 'File is required.';
    if (empty($params['filename'])) $errors['filename'] = 'Filename is required.';
    if (empty($errors)) {
      $data['success'] = true;
      $data['message'] = 'Success!';
      if ($params['superheroAlias'] === 'ND') {
        $data['data'] = $this->modelPost->create($params);
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
}
