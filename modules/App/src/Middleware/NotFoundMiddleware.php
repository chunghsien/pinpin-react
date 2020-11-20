<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Laminas\Db\Adapter\Adapter;

class NotFoundMiddleware implements MiddlewareInterface
{
    /** @var null|TemplateRendererInterface */
    protected $template;
    
    /**
     *
     * @var Adapter
     */
    //protected  $adapter;
    
    protected $defaultTemplate = 'app::site-default';
    
    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
        //$this->adapter = $adpater;
    }
    

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if(isAjax() || isApiRequest()) {
            return new ApiErrorResponse(404, [], 'Page not found.');
        }
        $tmp = explode(',', $request->getHeaders()['accept-language'][0]);
        $lang = $tmp[0];
        $react_base = is_file('./resources/templates/app/site_base.html.twig') ?  : '@error/404';
        return new HtmlResponse($this->template->render('@error/404', ['lang' => $lang]), 404);
    }
}