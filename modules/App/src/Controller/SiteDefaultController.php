<?php

declare(strict_types = 1);

namespace App\Controller;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Chopin\Support\Registry;

class SiteDefaultController implements RequestHandlerInterface
{

    /** @var null|TemplateRendererInterface */
    private $template;

    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    //$PT.'_decrypt_users_profile' => $PT.'users_profile'
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $page_config = Registry::get('page_json_config');
        $site_base = is_file('./resources/templates/app/site_base.html.twig') ? '@app/site_base.html.twig' : '';
        $vars = [
            'layout' => false,
            '__csrf' => $request->getAttribute('csrf')->generateToken(),
            'html_lang' => $request->getAttribute('html_lang'),
            'page_json_config' => is_array($page_config)? $page_config : [],
            'site_base' => $site_base,
            'chronicle_year' => date("Y")
        ];
        return new HtmlResponse($this->template->render('app::site-default', $vars));
    }
}
