<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Chopin\SystemSettings\TableGateway\SystemSettingsTableGateway;
use Chopin\LanguageHasLocale\TableGateway\LanguageTableGateway;
use Chopin\I18n\LangType;
use Laminas\Cache\Storage\StorageInterface;
use Chopin\I18n\Translator\Translator;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;
use Chopin\LaminasDb\DB\Traits\CacheTrait;
use Chopin\Support\Registry;

class SystemSettingsMiddleware implements MiddlewareInterface
{

    use CacheTrait;
    use \App\Traits\I18nTranslatorTrait;

    /**
     *
     * @var null|SystemSettingsTableGateway
     */
    private $systemSettingsTableGateway;

    /**
     *
     * @var null|LanguageTableGateway
     */
    private $languageTableGateway;

    public function __construct(StorageInterface $cache, SystemSettingsTableGateway $systemSettingsTableGateway = null, LanguageTableGateway $languageTableGateway = null)
    {
        $this->systemSettingsTableGateway = $systemSettingsTableGateway;
        $this->languageTableGateway = $languageTableGateway;
        $this->initTranslator($cache);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $serverParams = $request->getServerParams();
        $accept_lang_matches = [];
        if (isset($serverParams['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match('/^(?P<lang>\w{2}\-\w{2})\,/i', $serverParams['HTTP_ACCEPT_LANGUAGE'], $accept_lang_matches);
        }
        $html_lang = isset($accept_lang_matches['lang']) ? $accept_lang_matches['lang'] : 'zh-TW';
        if ($request->getAttribute('lang')) {
            $lang = $request->getAttribute('lang');
            $html_lang = LangType::get($lang, LangType::HTML);
        }
        $php_lang = LangType::get($html_lang, LangType::PHP);
        $adapter = $this->languageTableGateway->adapter;

        if ($this->systemSettingsTableGateway instanceof SystemSettingsTableGateway && $this->languageTableGateway instanceof LanguageTableGateway) {

            $serialize = null;
            if ($this->getEnvCacheUse()) {
                $systemSettingsCacheKey = crc32($php_lang . $html_lang . $this->systemSettingsTableGateway->table . '_toSerialize');
                $serialize = $this->getCache($systemSettingsCacheKey);
            }
            if (! $serialize) {
                $serialize = $this->systemSettingsTableGateway->toSerialize();
                if ($this->getEnvCacheUse()) {
                    $this->saveDbCacheMapper($systemSettingsCacheKey, $this->systemSettingsTableGateway->getTailTableName());
                    $this->setCache($systemSettingsCacheKey, $serialize);
                }
                // traceVarsProgress($serialize);
            }
            // debug($serialize, ['output_type' => 'export']);
            mergePageJsonConfig([
                'stock_status' => config('stock_status'),
                'system_settings' => $serialize
            ]);
            // 第三方支付
            $thirdPayServiceClass = config('third_party_service.logistics.service_class');
            $thirdPayServiceReflection = new \ReflectionClass($thirdPayServiceClass);
            $languageHasLocaleTableGateway = new LanguageHasLocaleTableGateway($adapter);
            $languageHasLocaleRow = $languageHasLocaleTableGateway->select([
                'code' => $php_lang
            ])->current();
            $request = $request->withAttribute('language_id', $languageHasLocaleRow->language_id);
            $request = $request->withAttribute('locale_id', $languageHasLocaleRow->locale_id);
            $languageRow = $this->languageTableGateway->select([
                'id' => $languageHasLocaleRow->language_id
            ])->current();
            /**
             *
             * @var \Chopin\Store\Service\ThirdPartyPaymentService $thirdPayService
             */
            $thirdPayService = $thirdPayServiceReflection->newInstance($php_lang, $languageRow->code);

            $payMethodOptions = $thirdPayService->getPayMethodOptions($languageHasLocaleRow->language_id, $languageHasLocaleRow->locale_id);
            mergePageJsonConfig([
                'pay_method_options' => $payMethodOptions
            ]);
            mergePageJsonConfig([
                'third_party_service' => config('third_party_service')
            ]);
            $request = $request->withAttribute('system_settings', $serialize);
            //debug($serialize, ['output_type' =>'json']);
        }

        $request = $request->withAttribute('php_lang',  $php_lang);
        $request = $request->withAttribute('html_lang',  $html_lang);
        Registry::set('html_lang', $html_lang);
        if(!$request->getAttribute(Translator::class, null) && $this->translator instanceof Translator) {
            $request = $request->withAttribute(Translator::class,  $this->translator);
        }
        return $handler->handle($request);
    }
}