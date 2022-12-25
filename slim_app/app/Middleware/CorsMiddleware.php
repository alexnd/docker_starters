<?php
namespace App\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

/**
 * CORS middleware.
 */
final class CorsMiddleware implements MiddlewareInterface
{
    /**
     * Invoke middleware.
     *
     * @param ServerRequestInterface $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface The response
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        #_log('*cors middleware');
        $routeContext = RouteContext::fromRequest($request);
        $routingResults = $routeContext->getRoutingResults();
        $methods = $routingResults->getAllowedMethods() ?? ['*'];
        #https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Headers
        $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers') ?? '*';
        #_log($requestHeaders, 'requestHeaders');
        $referer = $request->getHeaderLine('Referer') ?? 'http://example.com/';
        #if (preg_match("!\/$!", $referer)) {
        #    $referer = substr($referer, 0,-1);
        #}
	$u = parse_url($referer);
	$host = (isset($u['host']) && !empty($u['host'])) ? $u['host'] : $_SERVER['SERVER_NAME'];
        $port = (isset($u['port']) && !empty($u['port'])) ? $u['port'] : '';
        $scheme = (isset($u['scheme'])) ? $u['scheme'] : 'http';
        $origin = $scheme . "://" . $host;
        if ($port) {
            $origin .= ':' . $port;
        }
        $response = $handler->handle($request);
        $response = $response
            // For security put there 'https://domain.com'
            // ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Methods', implode(', ', $methods))
            ->withHeader('Access-Control-Allow-Headers', $requestHeaders)
            // Optional: Allow Ajax CORS requests with Authorization header
            ->withHeader('Access-Control-Allow-Credentials', 'true');
        return $response;
    }
}
