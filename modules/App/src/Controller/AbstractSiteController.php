<?php

declare(strict_types = 1);

namespace App\Controller;

//use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Chopin\Support\Registry;
use Laminas\Db\Adapter\Adapter;
use Laminas\Diactoros\Response\RedirectResponse;

abstract class AbstractSiteController implements RequestHandlerInterface
{

    /** @var null|TemplateRendererInterface */
    protected $template;
    
    /**
     * 
     * @var Adapter
     */
    protected  $adapter;
    
    protected $defaultTemplate = 'app::site-default';
    
    public function __construct(TemplateRendererInterface $template, Adapter $adpater)
    {
        $this->template = $template;
        $this->adapter = $adpater;
    }

    protected  function responseStandBy(ServerRequestInterface $request)
    {
        $lang = $request->getAttribute('lang', null);
        if(!$lang) {
            $tmp = $request->getHeaders()['accept-language'][0];
            $lang = explode(',', $tmp)[0];
            return new RedirectResponse('/index/'.$lang);
        }
        $page_config = Registry::get('page_json_config');
        $react_base = is_file('./resources/templates/app/site_base.html.twig') ? '@app/site_base.html.twig' : '';
        $vars = [
            'layout' => false,
            '__csrf' => $request->getAttribute('csrf')->generateToken(),
            'html_lang' => $request->getAttribute('html_lang'),
            'page_json_config' => is_array($page_config)? $page_config : [],
            'react_base' => $react_base,
            'chronicle_year' => date("Y"),
        ];
        return $vars;
    }
    
    abstract public function handle(ServerRequestInterface $request): ResponseInterface ;
}
