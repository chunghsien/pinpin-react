<?php
declare(strict_types = 1);

namespace App\Controller;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\LaminasView\LaminasViewRenderer;
use Chopin\Support\Registry;
use Laminas\Db\Adapter\Adapter;
use Chopin\LanguageHasLocale\TableGateway\LanguageHasLocaleTableGateway;
use Mezzio\Session\SessionMiddleware;

class AdminDefaultController implements RequestHandlerInterface
{

    use Traits\AdminTrait;
    
    /** @var null|LaminasViewRenderer */
    private $template;

    /**
     *
     * @var Adapter
     */
    private $adapter;

    public function __construct(TemplateRendererInterface $template, Adapter $adapter)
    {
        $this->template = $template;
        $this->adapter = $adapter;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $php_lang = str_replace('-', '_', $request->getAttribute('html_lang'));
        $vars = [
            'layout' => false,
            '__csrf' => $request->getAttribute('csrf')->generateToken(),
            'html_lang' => $request->getAttribute('html_lang'),
            'site_name' => $request->getAttribute('system_settings')['site_info'][$php_lang]['children']['name']['value'],
        ];
        $page_config = Registry::get('page_json_config');

        // 將語言選取加入(前端select option資料使用)
        $languageHasLocaleTableGateway = new LanguageHasLocaleTableGateway($this->adapter);
        $languageOptions = $languageHasLocaleTableGateway->getOptions([
            'locale_id',
            'language_id'
        ], 'display_name', [], [
            [
                'equalTo',
                'AND',
                [
                    'is_use',
                    1
                ]
            ]
        ]);
        $page_config['languageOptions'] = $languageOptions;
        if (is_array($page_config) && $page_config) {
            /**
             *
             * @var \Mezzio\Session\LazySession $session
             */
            $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
            $admin = $session->get('admin');
            $page_config['admin'] = $admin;
            $vars['page_json_config'] = $page_config;
        }

        return new HtmlResponse($this->template->render('app::admin-default', $vars));
    }
}
