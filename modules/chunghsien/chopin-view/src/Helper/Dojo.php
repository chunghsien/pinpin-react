<?php

namespace Chopin\View\Helper;

use Laminas\View\Helper\AbstractHelper;
use Laminas\Escaper\Escaper;

/**
 * 
 * @deprecated
 *
 */
class Dojo extends AbstractHelper
{
    protected $regKey = 'Pinwin_View_Helper_Dojo';

    protected $config;

    protected $baseUrl;

    protected $boosts = '';

    /**
     *
     * @var \LaminasEscaper\Escaper
     */
    protected $escaper;

    public function __construct($config = [])
    {
        $this->config = $config;
        if (isset($config['baseUrl'])) {
            $this->baseUrl = str_replace('/packages', '', $config['baseUrl']);
        }
        if (defined('IS_DEVELOPMENT')) {
            $this->boosts = "?r=" . date("His");
        }
        $this->escaper = new Escaper();
    }

    public function __invoke()
    {
        return $this;
    }

    public function parseProps(array $propos, $isHTMLEntities = true)
    {
        $json = json_encode($propos);
        return $this->Json2Props($json, $isHTMLEntities);
    }

    public function printProps(array $propos)
    {
        $json2Props = $this->parseProps($propos);
        return sprintf("data-dojo-props=\"%s\"", $json2Props);
    }

    protected function Json2Props(string $json, $isHTMLEntities = true)
    {
        $json = preg_replace('/^\{/', '', $json);
        $json = preg_replace('/\}$/', '', $json);
        if ($isHTMLEntities) {
            return htmlentities($json, ENT_QUOTES, 'UTF-8');
        } else {
            $json = str_replace('"', "'", $json);
            return $json;
        }
    }

    protected function isFileExist($src)
    {
        if (is_dir('./public')) {
            $src = './public/' . preg_replace('/^\//', '', $src);
        }

        return is_file($src);
    }

    public function printScript($src = '')
    {
        if (count($this->config) === 0 || ! isset($this->config['baseUrl'])) {
            $this->config['baseUrl'] = $this->view->basePath('');
        }

        $config = json_encode($this->config);

        $config = $this->Json2Props($config);

        $src = trim($src);
        switch ($src) {
            case '':
                $src = $this->baseUrl . '/packages/dojo/dojo/dojo.js';
                break;
        }

        if ( ! $this->isFileExist($src)) {
            throw new \ErrorException('The file: ' . $src . ' not exist.');
        }
        $attr = $this->escaper->escapeHtmlAttr("text/javascript");
        if (false === strpos($src, 'dojo.js')) {
            if (IS_DEVELOPMENT) {
                $src .= '.uncompress.js';
            }
            $src = $this->escaper->escapeHtmlAttr($src . $this->boosts);
            return sprintf("<script type=\"%s\" src=\"%s\"></script>\n", $attr, $src);
        } else {
            $src = $this->escaper->escapeHtmlAttr($src);
            return sprintf("<script type=\"%s\" src=\"%s\" data-dojo-config=\"%s\"></script>\n", $attr, $src, $config);
        }
    }
}
