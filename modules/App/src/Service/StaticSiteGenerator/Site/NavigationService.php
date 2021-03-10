<?php

namespace App\Service\StaticSiteGenerator\Site;

use Laminas\Db\Adapter\Adapter;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\StaticSiteGenerator\AbstractService;
use Laminas\I18n\Translator\Translator;

class NavigationService extends AbstractService
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter;
    }

    public function result(ServerRequestInterface $request)
    {
        $lang = $request->getAttribute('lang');
        $path = "./modules/App/options/{$lang}_site.navigation.php";
        $data = include $path;
        return [
            "navigation" => $data,
        ];
    }
}