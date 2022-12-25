<?php
namespace App\Actions;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;
use Odan\Session\SessionInterface;

final class LogoutAction {
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    public function __invoke(
        ServerRequestInterface $request, 
        ResponseInterface $response
    ): ResponseInterface {
        // Logout user
        $this->session->destroy();
        // TODO: if json request just send json responce
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        // $url = $routeParser->urlFor('login');
        $url = '/';
        if (is_xhr_req($request)) {
          return res_json($response, ['status' => true], 200);
        } else {
          return res_redirect($response, $url);
        }
    }
}