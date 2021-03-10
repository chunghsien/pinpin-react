<?php
declare(strict_types = 1);

namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use App\Service\AjaxFormService;
use Chopin\Documents\TableGateway\FacebookTagsTableGateway;
use Laminas\Diactoros\Response\EmptyResponse;
use Intervention\Image\ImageManagerStatic;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;
use Laminas\Diactoros\Response\JsonResponse;

class FacebookTagsAction extends AbstractAction
{

    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    protected function get(ServerRequestInterface $request): ResponseInterface
    {
        $ajaxFormService = new AjaxFormService();
        $response = $ajaxFormService->getProcess($request, new FacebookTagsTableGateway($this->adapter));
        if($response instanceof EmptyResponse) {
            return $response;
        }
        $data = $response->getPayload()['data'];
        return new ApiSuccessResponse(0, $data);
    }
    
    private function afterSaved(JsonResponse $response, FacebookTagsTableGateway $tablegateway)
    {
        $data = $response->getPayload()['data'];
        $imagePath = './public'.$data['og_colon_image'];
        $row = $tablegateway->select(['id' => $data['id']])->current();
        $siteUrl = siteBaseUri();
        if(is_file($imagePath)) {
            $ogImageManager = ImageManagerStatic::make($imagePath);
            $row->og_colon_image_colon_width = $ogImageManager->width();
            $row->og_colon_image_colon_height = $ogImageManager->height();
            $row->og_colon_image_colon_type = $ogImageManager->mime;
            
            if(preg_match('/^https/', $siteUrl)) {
                $row->og_colon_image_colon_secure_url = $siteUrl.'/'.$data['og_colon_image'];
            }
        }
        $videoPath = './public'.$data['og_colon_video'];
        if(is_file($videoPath)) {
            if(preg_match('/^https/', $siteUrl)) {
                $row->og_colon_video_colon_secure_url = $siteUrl.'/'.$data['og_colon_video'];
            }
        }
        $table = $row->toArray()['table'];
        $table_id = $row->table_id;
        $tableClassTableGateway = AbstractTableGateway::newInstance($table, $this->adapter);
        $tableClassRow = $tableClassTableGateway->select(['id' => $table_id])->current();
        
        $languageHasLocaleTableGateway = new LanguageHasLocaleTableGateway($this->adapter);
        $languageHasLocaleRow = $languageHasLocaleTableGateway->select([
            'language_id' => $tableClassRow->language_id,
            'locale_id' => $tableClassRow->locale_id]
        )->current();
        
        $code = $languageHasLocaleRow->code;
        $row->og_colon_locale = $code;
        $row->save();
        //$response = $response->withPayload($row->toArray());
        return new ApiSuccessResponse(0, $row->toArray(), $response->getPayload()['message']);
        
    }
    
    protected function put(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->adapter->getDriver()->getConnection()->beginTransaction();
            $ajaxFormService = new AjaxFormService();
            $tablegateway = new FacebookTagsTableGateway($this->adapter);
            $response = $ajaxFormService->putProcess($request, $tablegateway);
            if($response instanceof ApiErrorResponse) {
                $this->adapter->getDriver()->getConnection()->rollback();
                return $response;
            }
            $this->adapter->getDriver()->getConnection()->commit();
            $response = $this->afterSaved($response, $tablegateway);
            $this->adapter->getDriver()->getConnection()->commit();
            return $response;
        } catch (\Exception $e) {
            loggerException($e);
            $this->adapter->getDriver()->getConnection()->rollback();
            return new ApiErrorResponse(1, [], ['message' => $e->getMessage()], [$e->getMessage()]);
        }
    }
    
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->adapter->getDriver()->getConnection()->beginTransaction();
            $queryParams = $request->getQueryParams();
            $post = $request->getParsedBody();
            if(isset($queryParams['put']) || isset($post['id'])) {
                return $this->put($request);
            }
            $ajaxFormService = new AjaxFormService();
            $tablegateway = new FacebookTagsTableGateway($this->adapter);
            $response = $ajaxFormService->postProcess($request, $tablegateway);
            if($response instanceof ApiErrorResponse) {
                $this->adapter->getDriver()->getConnection()->rollback();
                return $response;
            }
            $response = $this->afterSaved($response, $tablegateway);
            $this->adapter->getDriver()->getConnection()->commit();
            return $response;
        } catch (\Exception $e) {
            $this->adapter->getDriver()->getConnection()->rollback();
            loggerException($e);
            return new ApiErrorResponse(1, [], ['message' => $e->getMessage()], [$e->getMessage()]);
        }
    }
}
