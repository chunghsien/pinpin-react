<?php
declare(strict_types = 1);
namespace App\Controller\Api\Admin\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\Documents\TableGateway\BannerTableGateway;
use App\Service\AjaxFormService;
use Laminas\Diactoros\Response\JsonResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Laminas\I18n\Translator\Translator;
use Laminas\Validator\File\IsImage;

class EditorUploadAction extends AbstractAction
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = strtolower($request->getMethod());
        return $this->{$method}($request);
    }

    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $upload = $request->getUploadedFiles();
        if($upload['upload']->getSize() > 2097152) {
            $data = [
                "error" => [
                    "message" => '圖片大小超過2MB。',
                ],
            ];
        }else if($upload['upload']->getError() == 0){
            $formService = new AjaxFormService();
            $formService->processUpload($request, null, [
                new IsImage(),
            ]);
            $uploaded = $formService->getUploaded();
            $uploadResponse = $formService->getUploadResponse();
            if($uploadResponse instanceof ApiErrorResponse) {
                if(isset($uploadResponse->getPayload()['notify'])) {
                    $notify = implode($uploadResponse->getPayload()['notify'], '');
                    $data = [
                        "error" => [
                            "message" => $notify,
                        ],
                    ];
                    
                }else {
                    $data = [
                        "error" => [
                            "message" => '圖片上傳失敗',
                        ],
                    ];
                    
                }
            }else {
                $data = [
                    'fileName' => $upload['upload']->getClientFilename(),
                    'uploaded' => 1,
                    'url' => $uploaded['upload'],
                    
                ];
            }
        }else {
            $data = [
                "error" => [
                    "message" => '圖片上傳失敗',
                ],
            ];
        }
        return new JsonResponse($data);
    }
}
