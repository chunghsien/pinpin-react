<?php

use Mezzio\Template\TemplateRendererInterface;
use Mezzio\Twig\TwigEnvironmentFactory;
use Mezzio\Twig\TwigExtension;
use Twig\Environment;
use Chopin\View\ZendExpressive\Twig\TwigExtensionFactory;
use Chopin\View\ZendExpressive\Twig\TwigRendererFactory;

return [
    'dependencies' => [
        'aliases' => [
            Twig_Environment::class => Environment::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            // for templates setting
            TemplateRendererInterface::class => TwigRendererFactory::class,
            Environment::class => TwigEnvironmentFactory::class,
            TwigExtension::class    => TwigExtensionFactory::class,
        ],
    ],
];
