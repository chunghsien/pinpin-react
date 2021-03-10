<?php

namespace App\Controller\Traits;

use Mezzio\Router\RouteResult;

trait AdminTrait {
    
    /**
     * 
     * @param RouteResult $routeResult
     * @return mixed
     */
    protected function buildUri(RouteResult $routeResult, $to='', $prefix) {
        
        $lang = $routeResult->getMatchedParams()['lang'];
        $path = '';
        if(!preg_match('/admin\-(login|logout)$/', $to)) {
            $path = '/'.$lang.'/admin/'.$to;
            $path = preg_replace('/\/{2,}/', '/', $path);
        }
        if(preg_match('/admin\-(login|logout)$/', $to)) {
            
            $path = '/'.$lang.'/'.$to;
            $path = preg_replace('/\/{2,}/', '/', $path);
        }
        if($prefix) {
            $path = $prefix.$path;
            $path = preg_replace('/^\/{2,}/', '/', $path);
        }
        return $path;
    }
}