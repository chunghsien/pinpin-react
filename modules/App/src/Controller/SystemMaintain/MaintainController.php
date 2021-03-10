<?php
declare(strict_types = 1);

namespace App\Controller\SystemMaintain;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Template\TemplateRendererInterface;
use Laminas\Db\Adapter\Adapter;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\LaminasView\LaminasViewRenderer;
use Psr\Http\Server\RequestHandlerInterface;

class MaintainController implements RequestHandlerInterface
{

    /** @var null|LaminasViewRenderer */
    private $template;
    
    /**
     *
     * @var Adapter
     */
    private $adapter;
    
    public function __construct(TemplateRendererInterface $template, Adapter $adapter)
    {
        $this->template = $template;
        $this->adapter = $adapter;
    }
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $next_js_page = '';
        if(is_file('./resources/templates/app/system-maintain/maintain.html.twig'))
        {
            $next_js_page = 'app::system-maintain/maintain';
        }
        return new HtmlResponse($this->template->render('app::system-maintain'));
        //'app::system-maintain/maintain';
    }
}
