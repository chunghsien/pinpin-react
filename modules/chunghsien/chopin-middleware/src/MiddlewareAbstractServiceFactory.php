<?php

namespace Chopin\Middleware;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Mezzio\Router\RouterInterface;

class MiddlewareAbstractServiceFactory implements AbstractFactoryInterface
{

    /**
     *
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     *
     * {@inheritdoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $reflection = $this->reflection;
        $isConstruct = $reflection->hasMethod('__Construct');

        if ($isConstruct) {
            /**
             *
             * @var \ReflectionParameter[] $params
             */
            $params = $reflection->getMethod('__Construct')->getParameters();
            $args = [];
            foreach ($params as $param) {
                if (! $param->getClass()) {
                    return false;
                }

                $id = $param->getClass()->name;

                if ($container->has($id)) {
                    $arg = $container->get($id);
                }

                $args[] = $arg;
            }

            $middleware = $reflection->newInstanceArgs($args);
        } else {
            $middleware = $reflection->newInstance();
        }

        return $middleware;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Laminas\ServiceManager\Factory\AbstractFactoryInterface::canCreate()
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (! class_exists($requestedName)) {
            return false;
        }
        $config = $container->get('config');
        $this->reflection = new \ReflectionClass($requestedName);
        $reflection = $this->reflection;

        if ((class_exists('App\NoMVC\MiddlewareInterface') && $reflection->implementsInterface('App\NoMVC\MiddlewareInterface')) || (class_exists('App\MiddlewareInterface') && $reflection->implementsInterface('App\MiddlewareInterface')) || $reflection->implementsInterface(\Psr\Http\Server\RequestHandlerInterface::class)) {
            return true;
        }

        $isConstruct = $reflection->hasMethod('__Construct');
        if ($isConstruct) {
            /**
             *
             * @var \ReflectionParameter[] $params
             */
            $params = $reflection->getMethod('__Construct')->getParameters();

            foreach ($params as $param) {
                if (! $param->getClass()) {
                    return false;
                }
            }
        }

        if ($container->has('Psr\Http\Message\ServerRequestInterface') && $container->has(RouterInterface::class)) {
            $router = $container->get(RouterInterface::class);
            $requestCallback = $container->get('Psr\Http\Message\ServerRequestInterface');
            /**
             *
             * @var \Laminas\Diactoros\ServerRequest $request
             */
            $request = $requestCallback();
            if ($router->match($request)->isSuccess()) {
                if (class_exists($requestedName)) {
                    // process;
                    $reflection = new \ReflectionClass($requestedName);

                    if ($reflection->implementsInterface('Psr\Http\Server\MiddlewareInterface')) {
                        $find = explode('\\', $requestedName)[0];
                        // strpos($config, explode('\\', $requestedName)[0])
                        if (false !== array_search($find, $config)) {
                            return true;
                        }
                    }
                }
            } else {
                return false;
            }
        }

        return false;
    }
}
