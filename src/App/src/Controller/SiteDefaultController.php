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
        $vars = [
            'layout' => false,
            '__csrf' => $request->getAttribute('csrf')->generateToken(),
            'html_lang' => $request->getAttribute('html_lang'),
            'page_json_config' => is_array($page_config)? $page_config : [],
        ];
        return new HtmlResponse($this->template->render('app::site-default', $vars));
    }
}
