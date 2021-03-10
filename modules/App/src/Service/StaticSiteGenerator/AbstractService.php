<?php

namespace App\Service\StaticSiteGenerator;

use Laminas\Db\Adapter\Adapter;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractService
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    protected $data;
    
    
    abstract public function result(ServerRequestInterface $request);
}