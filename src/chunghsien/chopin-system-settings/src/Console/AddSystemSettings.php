<?php

namespace Chopin\SystemSettings\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Laminas\Db\Adapter\Adapter;

class AddSystemSettings extends Command
{
    use SystemSettingsTrait;

    protected static $defaultName = 'system-setting:add';

    /**
     *
     * @var Adapter
     */
    protected $adapter;

    public function __construct()
    {
        global $container;
        $this->adapter = $container->get(Adapter::class);
        parent::__construct();
    }

    protected function configure()
    {
        //$this->addArgument('list', InputArgument::OPTIONAL);
        $this->addOption('language', null, InputOption::VALUE_REQUIRED, '資料表內的language_id');
        $this->addOption('locale', null, InputOption::VALUE_OPTIONAL, '資料表內的locale_id');
        $this->setDescription("新增其他語系的系統設定");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();

        if (isset($options['language'])) {
            $language = intval($options['language']);
            $locale = 0;

            if (isset($options['locale'])) {
                $locale = intval($options['locale']);
            }

            $connection = $this->adapter->getDriver()->getConnection();
            try {
                $connection->beginTransaction();
                $response = $this->addGeneralSeo($this->adapter, $language, $locale);
                if ($response instanceof \Exception) {
                    /**
                     * @var \Exception $response
                     */
                    $connection->rollback();
                    $output->writeln(sprintf('<error>%s</error>', $response->getMessage()));
                    loggerException($response);
                } else {
                    $output->writeln(sprintf('<info>%s</info>', $response));
                    $response = $this->addSiteInfo($this->adapter, $language, $locale);
                    if ($response instanceof \Exception) {
                        $connection->rollback();
                        $output->writeln(sprintf('<error>%s</error>', $response->getMessage()));
                        loggerException($response);
                    } else {
                        $output->writeln(sprintf('<info>%s</info>', $response));
                        $connection->commit();
                        exit();
                    }
                }
                $connection->rollback();
                exit();
            } catch (\Exception $e) {
                $connection->rollback();
                $output->writeln(spritnf('<error>%s</error>', $e->getMessage()));
                loggerException($e);
            }
        }
    }
}
