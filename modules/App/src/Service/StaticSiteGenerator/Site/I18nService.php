<?php

namespace App\Service\StaticSiteGenerator\Site;

use Laminas\Db\Adapter\Adapter;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\StaticSiteGenerator\AbstractService;
use Laminas\I18n\Translator\Translator;

class I18nService extends AbstractService
{

    /**
     *
     * @var Adapter
     */
    protected $adapter;
    
    /**
     * 
     * @var array
     */
    protected $text_domains;
    
    /**
     * 
     * @var Translator
     */
    protected $translator;
    
    public function __construct(
        Adapter $adapter,
        $text_domains
    )
    {
        $this->adapter;
        $this->text_domains = $text_domains;
        $textDomains = [];
        $translation_file_patterns = [];
        foreach ($text_domains as $pair) {
            if(false === array_search($pair, $textDomains)) {
                $textDomains[] = $pair;
            }
        }
        foreach ($textDomains as $tm) {
            $translation_file_patterns[] = [
                'type' => 'phpArray',
                'base_dir' => PROJECT_DIR . '/resources/languages',
                'pattern' => "%s/{$tm}.php",
                'text_domain' => "{$tm}",
            ];
        }
        $this->translator = Translator::factory([
            "translation_file_patterns" => $translation_file_patterns,
        ]);
        $this->translator->setLocale(BACKEND_LOCALE);
    }
    
    public function result(ServerRequestInterface $request)
    {
        $allMessage = [];
        foreach ($this->text_domains as $text_domain)
        {
            $messages = [
                $text_domain => (array)$this->translator->getAllMessages($text_domain)
            ];
            $allMessage = array_merge($allMessage, $messages);
        }
        return ["i18nLabels" => $allMessage];
    }

}