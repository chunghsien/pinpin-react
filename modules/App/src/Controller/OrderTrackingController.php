<?php

declare(strict_types = 1);

namespace App\Controller;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\RedirectResponse;

class OrderTrackingController extends AbstractSiteController
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $vars = $this->responseStandBy($request);
        if($vars instanceof RedirectResponse) {
            return $vars;
        }
        return new HtmlResponse($this->template->render($this->defaultTemplate, $vars));
    }
}
