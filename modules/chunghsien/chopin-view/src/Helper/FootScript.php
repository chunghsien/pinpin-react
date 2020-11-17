<?php

namespace Chopin\View\Helper;

//use Cbschuld\Browser;
use Laminas\View\Helper\HeadScript;

/**
 *
 * @deprecated
 *
 */
class FootScript extends HeadScript
{
    protected $regKey = 'Pinwin_View_Helper_FootScript';

    protected $config;

    public function __construct($config = [])
    {
        $this->config = $config;
        $this->setSeparator(PHP_EOL);
    }

    public function appendDojo($src)
    {
        if (count($this->config) === 0  || ! isset($this->config['baseUrl'])) {
            $this->config['baseUrl'] = $this->view->basePath().'/';
        }


        $config = json_encode($this->config);
        $config = preg_replace('/^\{/', '', $config);
        $config = preg_replace('/\}$/', '', $config);

        $this->setAllowArbitraryAttributes(true);
        $this->setFile(
            $src,
            'text/javascript',
            ['data-dojo-config'=>$config]
        );

        return $this;
    }
}
