<?php

namespace Chopin\LanguageHasLocale\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class I18nTranslationTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'i18n_translation';
}
