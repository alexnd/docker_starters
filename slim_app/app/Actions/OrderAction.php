<?php

namespace App\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Odan\Session\SessionInterface;
use App\Services\CustomerService;
use App\Services\OrderService;

final class OrderAction
{
    /**
     * @var SessionInterface
     */
    private $session;
    private $serviceCustomer;
    private $serviceOrder;

    public function __construct(
        CustomerService $serviceCustomer,
        OrderService $serviceOrder,
        SessionInterface $session
    )
    {
        $this->serviceCustomer = $serviceCustomer;
        $this->serviceOrder = $serviceOrder;
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
        $data = $this->serviceOrder->getAll();
        return res_json($res, $data, 200);
    }

    public function getNextOrderId(ServerRequestInterface $req, ResponseInterface $res)
    {
        # $errors = [];
        # try {} catch (e) {} finally {}
        $data = [
            'id' => $this->serviceOrder->getNextId()
        ];
        return res_json($res, $data, 200);
    }

    public function getOrder(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $id = $req->getAttribute('id');
        $data = $this->serviceOrder->get($id);
        return res_json($res, $data, 200);
    }

    public function createOrder(ServerRequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        $params = (array)$req->getParsedBody();
        _log_truncate();
        _log($params, 'CREATE');
        #$id = $this->serviceOrder->create($params);

        $id = -2;
        return res_json($res, ['orderId' => $id]);
    }
}
