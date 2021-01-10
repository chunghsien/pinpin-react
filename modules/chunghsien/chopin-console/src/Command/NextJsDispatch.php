<?php

namespace Chopin\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// use Symfony\Component\Console\Input\InputOption;
// use Laminas\Filter\Word\SeparatorToDash;

// use Symfony\Component\Console\Question\ConfirmationQuestion;
class NextJsDispatch extends Command
{

    protected static $defaultName = 'next-js-dispatch';

    protected function configure()
    {
        $this->setDescription("佈署Next.js至後端");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if (is_file('./storage/out/.htaccess')) {
                unlink('./storage/out/.htaccess');
            }
            if (is_file('./storage/out/web.config')) {
                unlink('./storage/out/web.config');
            }
            if (is_file('./storage/out/index.php')) {
                unlink('./storage/out/index.php');
            }
            if (is_file('./storage/out/favicon.ico')) {
                unlink('./storage/out/favicon.ico');
            }

            if (is_dir('./storage/out/dist')) {
                recursiveRemoveFolder('./storage/out/dist');
            }
            if (is_dir('./storage/out/assets')) {
                recursiveRemoveFolder('./storage/out/assets');
            }
            if (is_dir('./storage/out/storage')) {
                recursiveRemoveFolder('./storage/out/storage');
            }
            if (is_dir('./public/_next')) {
                recursiveRemoveFolder('./public/_next');
            }
            moveFolder('./storage/out/_next', './public/_next');
            copy('./public/assets/icons/favicon.ico', './public/favicon.ico');
            $level1Htmls = glob('./storage/out/*.html');
            foreach ($level1Htmls as $oldname) {
                $newname = str_replace('storage/out', 'resources/templates/app/site', $oldname);
                $newname .= '.twig';
                $folder = dirname($newname);
                if(!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }
                rename($oldname, $newname);
            }
            $level2Htmls = glob('./storage/out/**/*.html');
            foreach ($level2Htmls as $oldname) {
                $newname = str_replace('storage/out', 'resources/templates/app/site', $oldname);
                $newname .= '.twig';
                $folder = dirname($newname);
                if(!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }
                rename($oldname, $newname);
            }
            $level3Htmls = glob('./storage/out/**/**/*.html');
            foreach ($level3Htmls as $oldname) {
                $newname = str_replace('storage/out', 'resources/templates/app/site', $oldname);
                $newname .= '.twig';
                $folder = dirname($newname);
                if(!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }
                rename($oldname, $newname);
            }

            $level4Htmls = glob('./storage/out/**/**/**/*.html');
            foreach ($level4Htmls as $oldname) {
                $newname = str_replace('storage/out', 'resources/templates/app/site', $oldname);
                $newname .= '.twig';
                $folder = dirname($newname);
                if(!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }
                rename($oldname, $newname);
            }
            //moveFolder('./storage/out/site', './resources/templates/app/site');
            $output->writeln("<info>Next.js 佈署成功</info>");
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
    }
}
