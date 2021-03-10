<?php

namespace App\Service\StaticSiteGenerator\Site;

use Laminas\Db\Adapter\Adapter;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\StaticSiteGenerator\AbstractService;

class FooterService extends AbstractService
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    protected $data;
    
    public function __construct(Adapter $adapter)
    {
        $this->adapter;
        $this->data = [
            "company" => "lezada",
            "tel" => "(+00) 123 567990",
            "email" => "contact@lezada.com",
            "social" => [
                "twitter" => [
                    "use" => true,
                    "uri" => "https://www.twitter.com",
                ],
                "fb" => [
                    "use" => true,
                    "uri" => "https://www.facebook.com",
                ],
                "ig" => [
                    "use" => true,
                    "uri" => "https://www.instagram.com",
                ],
                "yt" => [
                    "use" => true,
                    "uri" => "https://www.youtube.com",
                ],
            ]
        ];
    }
    
    public function result(ServerRequestInterface $request)
    {
        return [
            "footer" => $this->data,
        ];
    }
}