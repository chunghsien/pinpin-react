<?php

namespace App\Service\StaticSiteGenerator\Site;

use Laminas\Db\Adapter\Adapter;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\StaticSiteGenerator\AbstractService;
use Chopin\Store\TableGateway\NpClassTableGateway;

class CategoriesService extends AbstractService
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;
    
    /**
     *
     * @var NpClassTableGateway
     */
    protected $npClassTableGateway;
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->npClassTableGateway = new NpClassTableGateway($adapter);
    }

    public function result(ServerRequestInterface $request)
    {
        $theme = config('theme.name');
        $language_id = $request->getAttribute('language_id', 119);
        $locale_id = $request->getAttribute('locale_id', 119);
        $code = $request->getAttribute('html_lang');
        return [
            "categoryData" => $this->npClassTableGateway->getLezadaCategory($language_id, $locale_id, $code),
        ];
        
    }
}