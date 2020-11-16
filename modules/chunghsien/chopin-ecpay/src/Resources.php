<?php

namespace Chopin\Ecpay;

final class Resources {
    static private function getBasePath() {
        return dirname(__DIR__);
    }
    
    static public function requiureClass($staticPath, $isUseBasePath = true)
    {
        if($isUseBasePath) {
            if(preg_match('/^\//', $staticPath)) {
                require_once self::getBasePath().$staticPath;
            }else {
                require_once self::getBasePath().'/'.$staticPath;
            }
            
        }else {
            require_once $staticPath;
        }
    }
}