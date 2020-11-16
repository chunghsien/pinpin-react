<?php

namespace Chopin\Validator;

use Laminas\I18n\Translator\TranslatorInterface as I18nTranslatorInterface;
use Laminas\Validator\Translator\TranslatorInterface;
use Laminas\I18n\Translator\Translator as I18nTranslator;

class Translator extends I18nTranslator implements I18nTranslatorInterface, TranslatorInterface
{
    public static $default_locale = 'zh_TW';
}
