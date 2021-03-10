<?php

namespace App\Service\StaticSiteGenerator\Site;

use Laminas\Db\Adapter\Adapter;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\StaticSiteGenerator\AbstractService;

class SlidersService extends AbstractService
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
        $template = $request->getAttribute('method_or_id', 'decor');
        switch ($template) {
            case 'trending':
                return [
                    "heroSliderData" => json_decode(file_get_contents(__DIR__ . '/hero-slider-two.json'), true),
                ];
                break;
            case 'smart-design':
                return [
                    "heroSliderData" => json_decode(file_get_contents(__DIR__ . '/hero-slider-six.json'), true),
                ];
                break;
            case 'essentials':
                return [
                    "heroSliderData" => json_decode(file_get_contents(__DIR__ . '/hero-slider-three.json'), true),
                ];
                break;
            case 'collection':
                return [
                    "heroSliderData" => json_decode(file_get_contents(__DIR__ . '/hero-slider-seven.json'), true),
                ];
                break;
            case 'perfumes':
                return [
                    "heroSliderData" => json_decode(file_get_contents(__DIR__ . '/hero-slider-four.json'), true),
                ];
                break;
            case 'furniture':
                return [
                    "heroSliderData" => json_decode(file_get_contents(__DIR__ . '/hero-slider-five.json'), true),
                ];
                break;
            case 'decor':
                return [
                    "heroSliderData" => json_decode(file_get_contents(__DIR__ . '/hero-slider-one.json'), true),
                ];
            default:
                return [];
        }
    }

}