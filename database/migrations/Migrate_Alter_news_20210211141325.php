<?php

use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Ddl\Constraint;
use Chopin\LaminasDb\Console\Migrations\AbstractMigration;
use Chopin\LaminasDb\Sql\Ddl\Column\MySQL\MySQLColumnFactory;
use function ReCaptcha\RequestMethod\file_get_contents;

class Migrate_Alter_news_20210211141325 extends AbstractMigration
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
    protected $table = 'news';
    
    protected $priority = 3;
    
    public function up()
    {
        $contents = file_get_contents("database/sql_seeds/news.html");
    }
    
    public function down()
    {
        //
    }
}
