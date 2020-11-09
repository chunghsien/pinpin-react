<?php

use Chopin\LaminasDb\Console\Migrations\AbstractMigration;

class Migrate_Alter_sys_settings_20200606021054 extends AbstractMigration
{
    /**
     * @desc create|drop|alter
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
        $smtpSettingPath = dirname(__DIR__).'/sql_seeds/Chopin_System_Settings_Insert7.php';
        require $smtpSettingPath;
        $seed = new \Chopin_System_Settings_Insert7($this->adapter);
        $this->seed = $seed;
    }

    public function down()
    {
        //
    }
}
