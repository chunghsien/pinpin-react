<?php

use Chopin\LaminasDb\Console\Migrations\AbstractMigration;

class Migrate_Alter_system_settings_20200907085722 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'alter';

    protected $priority = 3;

    /**
     *
     * @var string
     */
    protected $table = 'system_settings';

    public function up()
    {
        $smtpSettingPath = dirname(__DIR__) . '/sql_seeds/Chopin_System_Settings_Insert8.php';
        require $smtpSettingPath;
        $seed = new \Chopin_System_Settings_Insert8($this->adapter);
        $this->seed = $seed;
    }

    public function down()
    {
        //
    }
}
