<?php

namespace Chopin\I18n\Translator;

use Laminas\I18n\Translator\TranslatorInterface as I18nTranslatorInterface;
use Laminas\Validator\Translator\TranslatorInterface;
use Laminas\I18n\Translator\Translator as I18nTranslator;

class Translator extends I18nTranslator implements I18nTranslatorInterface, TranslatorInterface
{
    const DEFAULT_LANGUAGE = 'zh_Hant';

    const DEFAULT_LOCALE = 'zh_Hant_TW';

    public static $setting_locale = 'zh_Hant_TW';
    public static $setting_language = 'zh_Hant';

    public function getAllMessages($textDomain = null, $locale = null)
    {
        if (is_null($textDomain) && is_null($locale)) {
            if (preg_match('/^production/i', APP_ENV) && is_file('storage/cache/translator_package.dat')) {
                $translator_package = file_get_contents('storage/cache/translator_package.dat');
                return unserialize($translator_package);
            } else {
                $domains = [];
                if ($this->files) {
                    $_domians = array_keys($this->files);
                    $domains = array_merge($_domians);
                    foreach ($_domians as $text_domain) {
                        $this->loadMessagesFromFiles($text_domain, $this->locale);
                    }
                }
                if ($this->patterns) {
                    $_domians = array_keys($this->patterns);
                    $domains = array_merge($_domians);
                    foreach ($_domians as $text_domain) {
                        $this->loadMessagesFromPatterns($text_domain, $this->locale);
                    }
                }
                $domains = array_values($domains);
                $messages = [];
                foreach ($domains as $text_domain) {
                    $messages[$text_domain] = $this->messages[$text_domain][$this->locale];
                }
                file_put_contents('storage/cache/translator_package.dat', serialize($messages));
                return $messages;
            }
        }
        if ($textDomain == null) {
            $textDomain = 'default';
        }
        return parent::getAllMessages($textDomain, $locale);
    }
}
