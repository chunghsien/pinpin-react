<?php

namespace App\Traits;

use Laminas\I18n\Translator\Translator;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Cache\Storage\Adapter\Filesystem;
use Laminas\Diactoros\ServerRequest;

Trait I18nTranslatorTrait {
    
/**
     *
     * @var Translator
     */
    protected $translator;
    
    protected function initTranslator(StorageInterface $cache = null, $isInjectConfig=true)
    {
        if($cache instanceof Filesystem) {
            $dir = './storage/cache/app/i18n';
            if(!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $cache->setOptions(['cache_dir' => './storage/cache/app/i18n']);
        }
        if($isInjectConfig === true) {
            $this->translator = Translator::factory(config('translator'));
        }

        if(is_array($isInjectConfig)) {
            $config = $isInjectConfig;
            $this->translator = Translator::factory($config);
        }
        if(APP_ENV === 'production') {
            $this->translator->setCache($cache);
        }else {
            $this->translator->setCache(null);
        }
    }
    
    protected function getTranslator(ServerRequest  $request) {
        if($this->translator instanceof Translator) {
            return $this->translator;
        }
        return $request->getAttribute(Translator::class);
    }
}