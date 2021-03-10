<?php

namespace App\Traits;

use Laminas\I18n\Translator\Translator;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\Storage\Adapter\Filesystem;
use Laminas\Diactoros\ServerRequest;
use Laminas\Cache\StorageFactory;

Trait I18nTranslatorTrait {
    
/**
     *
     * @var Translator
     */
    protected $translator;
    
    protected function initTranslator(StorageInterface $cache = null, $isInjectConfig=true)
    {
        if(!$cache || $cache instanceof Filesystem) {
            $dir = './storage/cache/app/i18n';
            if(!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            if(!$cache) {
                $cache = StorageFactory::factory([
                    'adapter' => [
                        'name' => 'filesystem',
                        'options' => [
                            'dir_level' => 1,
                            'cache_dir' => './storage/cache/app/i18n',
                            'ttl' => 86400 * 7, //one week
                        ],
                    ],
                    'plugins' => [
                        'Serializer',
                    ],
                ]);
            }else {
                $cache->setOptions(['cache_dir' => './storage/cache/app/i18n']);
            }
        }
        //debug(config('translator'));
        if($isInjectConfig === true) {
            $this->translator = Translator::factory(config('translator'));
        }

        if(is_array($isInjectConfig)) {
            $config = $isInjectConfig;
            $this->translator = Translator::factory($config);
        }
        if(APP_ENV === 'production') {
            $this->translator->setCache($cache);
        }
    }
    
    protected function getTranslator(ServerRequest  $request) {
        if($this->translator instanceof Translator) {
            return $this->translator;
        }
        return $request->getAttribute(Translator::class);
    }
}