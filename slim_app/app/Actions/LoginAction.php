<?php
namespace App\Actions;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

final class LoginAction {
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
        #_log('*login action');
        $data = (array)$request->getParsedBody();
        $username = (string)($data['username'] ?? '');
        $password = (string)($data['password'] ?? '');
        $user = null;
        $role = 'guest';
        $token = null;
        $users = setting('auth');
        foreach ($users as $u) {
            if ($username === $u['login'] && $password === $u['password']) {
                $user = $username;
                $role = $u['role'] ?? null;
            }
        }
        // Clear all flash messages
        $flash = $this->session->getFlash();
        $flash->clear();
        // Get RouteParser from request to generate the urls
        $routeParser = RouteContext::fromRequest($request)->getRouteParser();
        if ($user) {
            // Login successfully
            // Clears all session data and regenerate session ID
            $this->session->destroy();
            $this->session->start();
            $this->session->regenerateId();
            $token = csrf_token();
            // TODO: use service to save in DB 
            $this->session->set('user', $username);
            $this->session->set('role', $role);
            $this->session->set('csrf_token', $token);
            $flash->add('success', 'Login successfully');
            // Redirect to protected page
            $url = $routeParser->urlFor('cabinet');
        } else {
            $flash->add('error', 'Login failed!');
            // Redirect back to the login page
            $url = $routeParser->urlFor('login');
        }
        if (is_xhr_req($request)) {
            if ($user) {
                return res_json($response, [
                    'user' => $username,
                    'role' => $role,
                    'csrf_token' => $token
                ], 200);
            } else {
                return res_json($response, ['error' => 'Login failed'], 403);
            }
        } else {
            return res_redirect($response, $url);
        }
    }

    public function loginForm(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
	return view($res, 'login', [
            // 'foo' => session('foo', '')
	]);
    }
}
