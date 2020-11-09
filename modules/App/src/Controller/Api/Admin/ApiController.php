<?php

namespace App\Controller\Api\Admin;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Filter\Word\DashToCamelCase;
use Laminas\Filter\Word\UnderscoreToCamelCase;

class ApiController implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $action = $request->getAttribute('action');
        $dashToCamelCase = new DashToCamelCase();
        $action = $dashToCamelCase->filter($action);
        $underscoreToCamelCase = new UnderscoreToCamelCase();
        $action = $underscoreToCamelCase->filter($action);
        $action = ucfirst($action);
        $classname = __NAMESPACE__.'\\Actions\\'.$action.'Action';
        $reflection = new \ReflectionClass($classname);
        $instance = $reflection->newInstance();
        return $instance->handle($request);
    }
    
}