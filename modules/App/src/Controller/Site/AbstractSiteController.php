<?php

declare(strict_types = 1);

namespace App\Controller\Site;

//use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Chopin\Support\Registry;
use Laminas\Db\Adapter\Adapter;
use Laminas\Diactoros\Response\RedirectResponse;
use App\Minifier\TinyMinify;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;

abstract class AbstractSiteController implements RequestHandlerInterface
{

    /** @var \Mezzio\Twig\TwigRenderer */
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
    
    protected function getNextJsBuildId()
    {
        foreach (glob('./public/_next/**') as $path) {
            $hash = preg_replace('/^\.\/public\/_next\//', '', $path);
            if($hash != 'data' && $hash != 'static') {
                return $hash;
            }
        }
    }
    protected function minifyRender($template, $vars)
    {
        $html = $this->template->render($template, $vars);
        //return $html;
        return TinyMinify::html($html);
    }

    protected  function responseStandBy(ServerRequestInterface $request)
    {
        $lang = $request->getAttribute('lang', null);
        $uri = preg_replace('/\/$/', '', $request->getUri()->getPath());
        $uri = preg_replace('/^\//', '', $uri);
        $uriExplod = explode('/', $uri);
        $page = 'index';
        if(count($uriExplod) >= 2) {
            $page = $uriExplod[1];
        }
        $param1 = $request->getAttribute('param1', null);
        $param2 = $request->getAttribute('param2', null);
        
        if(!$lang) {
            $tmp = $request->getHeaders()['accept-language'][0];
            $lang = explode(',', $tmp)[0];
            if(preg_match('/\-/', $lang)) {
                $tmp = explode('-', $lang);
                $tmp[1] = strtoupper($tmp[1]);
                $lang = implode('-', $tmp);
            }
            $languageHasLocaleTableGateway = new LanguageHasLocaleTableGateway($this->adapter);
            $code = preg_replace('/\-/', '_', $lang);
            $exists = $languageHasLocaleTableGateway->select([
                'code' => $code,
                'is_use' => 1
            ])->count();
            if($exists == 0) {
                $row = $languageHasLocaleTableGateway->select(['is_use' => 1])->current();
                $code = $row->code;
                $lang = preg_replace('/_/', '-', $code);
            }
            return new RedirectResponse('/'.$lang);
        }
        $page_config = Registry::get('page_json_config');
        $next_js_page = '';
        if($lang) {
            $next_js_page .= '@app/site/'.$lang;
            if($page && $page != 'index') {
                $next_js_page.= '/'.$page;
            }
            if($param1) {
                $next_js_page.= '/'.$param1;
            }
            if($param2) {
                $next_js_page.= '/'.$param2;
            }
            $next_js_page .= '.html.twig';
            
            $verify = preg_replace('/^@/', './resources/templates/', $next_js_page);
            if(!is_file($verify)) {
                $next_js_page = '';
            }
        }
        $vars = [
            'layout' => false,
            '__csrf' => $request->getAttribute('csrf')->generateToken(),
            'html_lang' => $request->getAttribute('html_lang'),
            'lang' => $lang,
            'page_json_config' => is_array($page_config)? $page_config : [],
            'next_js_page' => $next_js_page,
            'chronicle_year' => date("Y"),
        ];
        return $vars;
    }
    
    abstract public function handle(ServerRequestInterface $request): ResponseInterface ;
}
