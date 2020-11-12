<?php

namespace Chopin\I18n\Translator;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Interop\Container\ContainerInterface;

class TranslatorServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // Configure the translator
        $config     = $container->get('config');
        $trConfig   = isset($config['translator']) ? $config['translator'] : [];
        $translator = Translator::factory($trConfig);

        if ($container->has('TranslatorPluginManager')) {
            $translator->setPluginManager($container->get('TranslatorPluginManager'));
        }
        return $translator;
    }
}
