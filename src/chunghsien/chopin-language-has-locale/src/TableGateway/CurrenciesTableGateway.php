<?php

namespace Chopin\LanguageHasLocale\TableGateway;

use Chopin\LaminasDb\TableGateway\AbstractTableGateway;

class CurrenciesTableGateway extends AbstractTableGateway
{

    public static $isRemoveRowGatewayFeature = false;

    /**
     *
     * @inheritdoc
     */
    protected $table = 'currencies';
}
