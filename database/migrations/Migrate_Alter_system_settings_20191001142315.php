<?php

use Chopin\LaminasDb\Console\Migrations\AbstractMigration;

class Migrate_Alter_system_settings_20191001142315 extends AbstractMigration
{

    /**
     * create|drop|alter
     *
     * @var string
     */
    protected $type = 'alter';

    /**
     *
     * @var string
     */
    protected $table = 'system_settings';

    protected $priority = 3;

    public function up()
    {
        $this->runSeed();
    }

    public function runSeed()
    {
        $smtpSettingPath = dirname(__DIR__) . '/sql_seeds/Chopin_System_Settings_Insert5.php';
        require $smtpSettingPath;
        $seed = new \Chopin_System_Settings_Insert5($this->adapter);
        $this->seed = $seed;
    }

    public function down()
    {
        //
    }
}
