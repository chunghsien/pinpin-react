<?php

namespace Chopin\IlluminateDatabase\Console\Repository;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Laminas\Filter\Word\CamelCaseToDash;
//use Laminas\Code\Generator\ValueGenerator;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Metadata\Source;

class RepositoryInstall extends Command
{
    protected static $defaultName = 'illuminate-database-repository:install';

    /**
     *
     * @var Sql
     */
    protected $sql;

    public function __construct($name = null)
    {
        global $container;
        parent::__construct($name);
        $adapter = $container->get('Laminas\Db\Adapter\Adapter');
        $this->sql = new Sql($adapter);
    }

    protected function configure()
    {
        $this->addOption('prefix', null, InputOption::VALUE_REQUIRED, 'prefix namespace');
        $this->addOption('type', null, InputOption::VALUE_OPTIONAL, 'type: module|library');
        $this->setDescription("創建Laravel DB table Repository 從專案資料庫中");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $metadata = Source\Factory::createSourceFromAdapter($this->sql->getAdapter());
        $tables = $metadata->getTables();

        $options = $input->getOptions();
        $prefix = $options['prefix'];

        if (false === strpos($prefix, '\\', - 1)) {
            $prefix .= '\\';
        }

        $namespace = $prefix . 'Repositories';

        $type = $options['type'];
        if (is_null($type)) {
            $type = 'module';
        }
        $underscoreToCamelCase = new UnderscoreToCamelCase();
        $camelCaseToDash = new CamelCaseToDash();
        foreach ($tables as $table) {
            // use \Illuminate\Database\Eloquent\SoftDeletes; //trait
            if ( ! preg_match('/migrations$/', $table->getName())) {
                $template = file_get_contents(__DIR__ . '/stubs/repository');
                $template = str_replace('{namespace}', $namespace, $template);
                $template = str_replace('{table}', $table->getName(), $template);
                $class = ucfirst($underscoreToCamelCase->filter($table->getName()));
                $template = str_replace('{class}', $class, $template);
                $full_model_class = (str_replace('Repositories', 'Models', $namespace)).'\\' . $class . 'Model';
                $template = str_replace('{full_model_class}', $full_model_class, $template);

                $savePathArr = explode('\\', $namespace);
                $folder = 'src';
                if ($type == 'module') {
                    $folder .= sprintf('/%s', strtolower($camelCaseToDash->filter($savePathArr[0])));
                    $folder .= sprintf('/%s', 'src');
                    for ($i = 1; $i < count($savePathArr); $i ++) {
                        $folder .= sprintf('/%s', $savePathArr[$i]);
                    }
                } else {
                    $folder .= sprintf('/%s', strtolower($camelCaseToDash->filter($savePathArr[0])));
                    $folder .= sprintf('/%s', strtolower($camelCaseToDash->filter($savePathArr[1])));
                    $folder .= sprintf('/%s', 'src');
                    for ($i = 2; $i < count($savePathArr); $i ++) {
                        $folder .= sprintf('/%s', $savePathArr[$i]);
                    }
                }
                if ( ! is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }
                $path = sprintf('%s/%sRepository.php', $folder, $class);


                if (is_file($path)) {
                    $question = new ConfirmationQuestion("<comment>$path: 已存在，是否要重新寫入？[y/n]</comment>", false);
                    $helper = $this->getHelper('question');

                    if ($helper->ask($input, $output, $question)) {
                        $result = file_put_contents($path, $template);
                        if ($result) {
                            $output->writeln("<info>$path : 建立成功</info>");
                        } else {
                            $output->writeln("<error>$path : 建立失敗</error>");
                        }
                    }
                } else {
                    $result = file_put_contents($path, $template);
                    if ($result) {
                        $output->writeln("<info>$path : 建立成功</info>");
                    } else {
                        $output->writeln("<error>$path : 建立失敗</error>");
                    }
                }
            }
        }
        //exit();
    }
}
