<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\ApiQueryService;
use Chopin\Middleware\AbstractAction;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;
use App\Service\AjaxFormService;
use Chopin\LanguageHasLocale\TableGateway\LanguageTableGateway;
use Laminas\Diactoros\Response\EmptyResponse;
use Chopin\LanguageHasLocale\TableGateway\LocaleTableGateway;

class LanguageAction extends AbstractAction
{

    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new LanguageTableGateway($this->adapter));
        if( !($response instanceof EmptyResponse) ) {
            return $response;
        }else {
            $apiQueryService = new ApiQueryService();
            return $apiQueryService->processPaginator(
                $request,
                'modules/App/scripts/db/admin/language.php',
                [
                    'code' => 'language_has_locale',
                    'display_name' => 'language_has_locale',
                    'is_use' => 'language_has_locale'
                ]
            );
        }
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::put()
     */
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $tablegateway = new LanguageHasLocaleTableGateway($this->adapter);
        $response = $ajaxFormService->putProcess($request, $tablegateway);
        $isUseResult = $tablegateway->select(['is_use' => 1]);
        $languageUseIn = [];
        $localeUseIn = [];
        foreach ($isUseResult as $use) {
            $languageUseIn[] = $use->language_id;
            $localeUseIn[] = $use->locale_id;
        }
        $languageTableGateway = new LanguageTableGateway($this->adapter);
        $languageTableGateway->update(['is_use' => 0]);
        $languageTableGateway->update(['is_use' => 1], ['id' => $languageUseIn]);
        $localeTableGateway = new LocaleTableGateway($this->adapter);
        $localeTableGateway->update(['is_use' => 0]);
        $localeTableGateway->update(['is_use' => 1], ['id' => $localeUseIn]);
        return $response;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Chopin\Middleware\AbstractAction::delete()
     */
    protected function delete(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        return $ajaxFormService->deleteProcess($request, new LanguageHasLocaleTableGateway($this->adapter));
    }
}
