<?php

namespace Chopin\Support;

use Illuminate\Support\ServiceProvider;
use Laminas\Filter\Word;

abstract class LaravelServiceProvider extends ServiceProvider
{
    /**
     *
     * @var string
     */
    protected $basepath;

    protected $namespace;

    public function __construct($app)
    {
        parent::__construct($app);

        $reflection = new \ReflectionObject($this);
        $this->basepath = dirname(dirname(dirname($reflection->getFileName())));
        $this->namespace = $reflection->getNamespaceName();
    }

    /*
    public function boot()
    {
        $this->autoLoadFrom();
    }

    protected function autoLoadFrom()
    {
        $basepath = $this->basepath;
        $routesPath = $basepath.'/routes';
        if( is_dir($routesPath) )
        {
            $routesIterator = new \DirectoryIterator($routesPath);
        }
        //$foldername = Str::camel($value)
    }
    */
}
