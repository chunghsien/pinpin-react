<?php

declare(strict_types = 1);
namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Cache\Storage\StorageInterface;
use Chopin\I18n\LangType;
use Laminas\I18n\Translator\Resources;
use Laminas\Filter\Word\UnderscoreToDash;

class ReactLocalesController implements RequestHandlerInterface
{
    
    use \App\Traits\I18nTranslatorTrait;

    public function __construct(StorageInterface $cache)
    {
        $this->initTranslator($cache);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $header_host = $request->getHeader('host')[0];
            $server_host = $_SERVER['HTTP_HOST'];
            if($header_host === $server_host)
            {
                $querys = $request->getQueryParams();
                $lng = $querys['lng'];
                $lng = LangType::get($lng, LangType::PHP);
                $ns_arr = explode(' ', $querys['ns']);
                $messages = [];
                $filter = new UnderscoreToDash();
                $html_lng = $filter->filter($lng);
                foreach ($ns_arr as $ns) {
                    if($ns == 'translation') {
                        $this->translator->addTranslationFilePattern('phpArray', Resources::getBasePath(), Resources::getPatternForValidator(), 'translation');
                        //庫存狀態
                        $this->translator->addTranslationFilePattern('phpArray', './modules/App/resources/languages', '%s/stock_status.php', 'translation');
                    }
                    $messages[$html_lng][$ns] = $this->translator->getAllMessages($ns, $lng);
                    if(!$messages[$html_lng][$ns]) {
                        unset($messages[$html_lng]);
                        $messages[$html_lng][$ns] = $this->translator->getAllMessages($ns, 'zh_TW');
                    }
                    
                    //if($ns == 'translation') {
                    $translation = $messages[$html_lng][$ns];
                    foreach ($translation as &$msg) {
                        $matcher = [];
                        preg_match_all('/(?P<plural>(\%\w+\%)){1,}/', $msg, $matcher);
                        if(isset($matcher['plural'])) {
                            $old = $matcher['plural'];
                            $new = preg_replace('/^\%/', '{{', $old);
                            $new = preg_replace('/\%$/', '}}', $new);
                            $msg = str_replace($old, $new, $msg);
                        }
                    }
                    //}
                }
                //header('Access-Control-Allow-Origin: http://localhost');
                return new JsonResponse($messages, 200);
            }else {
                return new JsonResponse([]);
            }
            
        } catch (\Exception $e) {
            debug($e->getTrace());
            exit();
        }
        
    }
}
