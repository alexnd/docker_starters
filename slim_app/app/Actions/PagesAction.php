<?php

namespace App\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Odan\Session\SessionInterface;
use App\Models\TestModel;

final class PagesAction
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
     * about page
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     * @return ResponseInterface The response
     */

    public function aboutHTML(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $data = [
            'content' => 'AppName'
        ];
        return view($res, 'about', $data);
    }

    public function termsHTML(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $data = [
            'content' => 'AppName'
        ];
        return view($res, 'terms', $data);
    }

    public function faqHTML(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $data = [
            'content' => 'AppName'
        ];
        return view($res, 'faq', $data);
    }
}
