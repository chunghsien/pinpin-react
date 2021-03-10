<?php

namespace App\Service\StaticSiteGenerator\Site;

use Laminas\Db\Adapter\Adapter;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\StaticSiteGenerator\AbstractService;

class BlogService extends AbstractService
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
        $theme = $request->getAttribute('method_or_id', 'decor');
        switch ($theme) {
            case 'trending':
            case 'essentials':
            case 'smart-design':
            case 'collection':
            case 'perfumes':
                return [
                    "blogData" => json_decode(file_get_contents(__DIR__ . '/blog-post-one.json'), true),
                ];
                break;
            default:
                return [];
        }
    }

}