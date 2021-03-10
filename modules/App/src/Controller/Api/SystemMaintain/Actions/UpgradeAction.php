<?php
declare(strict_types = 1);

namespace App\Controller\Api\SystemMaintain\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\Middleware\AbstractAction;
use Chopin\HttpMessage\Response\ApiSuccessResponse;
use Chopin\HttpMessage\Response\ApiErrorResponse;
use Chopin\Jwt\JwtTools;
use Firebase\JWT\JWT;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;
use Chopin\SystemSettings\TableGateway\DbCacheMapperTableGateway;

class UpgradeAction extends AbstractAction
{

    // admin/login
    /**
     *
     * {@inheritdoc}
     * @see \Chopin\Middleware\AbstractAction::get()
     */
    protected function post(ServerRequestInterface $request): ResponseInterface
    {
        $jwt = '';
        if($request->hasHeader('Authorization')) {
            $jwt = implode('', $request->getHeader('Authorization'));
            
        }else {
            $jwt = $request->getQueryParams()['authorization'];
        }
        $jwt = preg_replace('/^bearer /i', '', $jwt);
        
        $key = config('encryption.jwt_key');
        $alg = config('encryption.jwt_alg');
        $payload = JWT::decode($jwt, $key, [$alg]);
        if(JwtTools::verify($payload)) {
            /**
             * 
             * @var \Laminas\Diactoros\UploadedFile $upload
             */
            $upload = $request->getUploadedFiles()['upload'];
            if(!extension_loaded('zip')) {
                return new ApiErrorResponse(1, [], []);
            }
            $zip = new \ZipArchive();
            $moveToPath = './upload.zip';
            if(!$zip->open($moveToPath))
            {
                unlink($moveToPath);
                return new ApiErrorResponse(1, [], []);
            }
            //debug(is_dir(PROJECT_DIR.'/public'));
            $public = PROJECT_DIR;
            if(is_dir(PROJECT_DIR.'/public'))
            {
                $public = PROJECT_DIR.'/public';
            }
            
            $dist = $public.'/dist';
            $_next = $public.'/_next';
            if(is_dir($dist)) {
                recursiveRemoveFolder($dist);
            }
            if(is_dir($_next)) {
                recursiveRemoveFolder($_next);
            }
            $zip->extractTo(PROJECT_DIR);
            return new ApiSuccessResponse(0, []);
        }else {
            return new ApiErrorResponse(1, [], []);
        }
    }
}
