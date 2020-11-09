<?php 

namespace Chopin\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Laminas\Db\TableGateway\AbstractTableGateway;

abstract class AbstractAction implements RequestHandlerInterface {
    
    /**
     * 
     * @var Adapter
     */
    protected $adapter;
    
    /**
     * 
     * @var AbstractTableGateway[]
     */
    protected $tableGateways;
    
    public function __construct()
    {
        $this->adapter = GlobalAdapterFeature::getStaticAdapter();
    }
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }
    
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }
    
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }
    
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }
    
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        return new EmptyResponse(405);
    }

}