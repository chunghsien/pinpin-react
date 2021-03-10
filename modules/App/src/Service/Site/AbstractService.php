<?php

namespace App\Service\Site;

use Laminas\Db\Adapter\Adapter;
use Psr\Http\Message\ServerRequestInterface;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;
use Laminas\Db\RowGateway\RowGatewayInterface;
use Chopin\Support\Registry;

abstract class AbstractService
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    /**
     *
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     *
     * @var LanguageHasLocaleTableGateway
     */
    protected $languageHasLocaleTableGateway;

    /**
     *
     * @var RowGatewayInterface
     */
    protected $localeRow;
    
    public function __construct(Adapter $adapter, ServerRequestInterface $request)
    {
        $this->adapter = $adapter;
        $this->request = $request;
        $this->languageHasLocaleTableGateway = new LanguageHasLocaleTableGateway($this->adapter);
        $code = str_replace('-', '_', $request->getAttribute('lang', 'zh_TW'));
        $tmp = $this->languageHasLocaleTableGateway->select(['code' => $code])->current();
        if(!$tmp)
        {
            $tmp = $this->languageHasLocaleTableGateway->select(['is_use' => 1])->current();
        }
        $this->localeRow = $tmp;
    }
    
    /**
     * 
     * @param array $result
     * @param string $middleUri
     * @param string $slug
     * @return array
     */
    protected function addLocale($result, $middleUri, $slug=null)
    {
        $locale = Registry::get('html_lang');
        foreach ($result as &$item)
        {
            $slugValue = $item[$slug];
            $uri = "/{$locale}/{$middleUri}/{$slugValue}";
            $item['uri'] = $uri;
        }
        return $result;
    }
}