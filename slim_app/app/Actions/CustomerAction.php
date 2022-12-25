<?php

namespace App\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Odan\Session\SessionInterface;

use App\Services\CustomerService;
use Slim\Routing\RouteContext;

final class CustomerAction
{
    /**
     * @var SessionInterface
     */
    private $session;
    private $serviceCustomer;

    public function __construct(
        CustomerService $serviceCustomer,
        SessionInterface $session
    )
    {
        $this->serviceCustomer = $serviceCustomer;
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
        $data = $this->serviceCustomer->getAll();
        return res_json($res, $data, 200);
    }

    public function getCustomer(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $id = $req->getAttribute('id');
        $data = $this->serviceCustomer->get($id);
        return res_json($res, $data, 200);
    }

    public function find(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $query = $req->getAttribute('q');
        if (is_numeric($query)) {
            // TODO: if phone number format - search by it
            $data['customer'] = $this->serviceCustomer->get($query);
        } else {
            $data['customer'] = $this->serviceCustomer->find('lastName', $query);
        }
        return res_json($res, $data, 200);
    }
}
