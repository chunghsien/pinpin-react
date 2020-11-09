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
    
    protected function initTranslator(StorageInterface $cache)
    {
        if($cache instanceof Filesystem) {
            $dir = './storage/cache/app/i18n';
            if(!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $cache->setOptions(['cache_dir' => './storage/cache/app/i18n']);
        }
        $this->translator = Translator::factory(config('translator'));
        if(APP_ENV === 'production') {
            $this->translator->setCache($cache);
        }else {
            $this->translator->setCache(null);
        }
    }
    
    protected function getTranslator(ServerRequest  $request) {
        return $request->getAttribute(Translator::class);
    }
}