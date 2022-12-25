<?php
namespace App\Middleware;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

final class AuthMiddleware implements MiddlewareInterface {
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        ResponseFactoryInterface $responseFactory, 
        SessionInterface $session
    ) {
        $this->responseFactory = $responseFactory;
        $this->session = $session;
    }

    public function process(
        ServerRequestInterface $request, 
        RequestHandlerInterface $handler
    ): ResponseInterface {
        #_log('*auth middleware');
        if ($this->session->get('token')) {
            // User is logged in, bypass
            return $handler->handle($request);
        }
        // User is not logged in.
        if (is_xhr_req($request)) {
            // For JSON client return parsable responce with 403 status
            $response = $this->responseFactory->createResponse();
            return res_json(
              $response,
              [
                'success' => false,
                'error' => 'Login failed',
                'errors' => [
                  'login_failed' => 'Login failed'
                ]
              ],
              403
            );
        } else {
            // For others redirect to login page.
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url = $routeParser->urlFor('login');
            #return res_redirect($response, $url);
            /*return $this->responseFactory->createResponse()
              ->withStatus(301)
              ->withHeader('Location', $url);*/
            redirect($url);
            return $request;
        }
    }
}
