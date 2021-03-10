<?php
declare(strict_types = 1);

namespace App\Controller\Api\Site\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Mezzio\Csrf\CsrfMiddleware;

class CsrfAction extends AbstractAction
{

    // admin/login
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        /**
         *
         * @var \Mezzio\Csrf\SessionCsrfGuard $guard
         */
        $guard = $request->getAttribute(CsrfMiddleware::GUARD_ATTRIBUTE);
        
        return new ApiSuccessResponse(0, ['__csrf' => $guard->generateToken()]);
    }
    
}
