<?php

// TODO: use namespace:
namespace App\Minifier;
//use er\TinyHtmlMinifier;

class TinyMinify
{
    public static function html(string $html, array $options = []) : string
    {
        $minifier = new TinyHtmlMinifier($options);
        if(APP_ENV === 'production') {
            return $minifier->minify($html);
        }else {
            return $html;
        }
        
    }
}
