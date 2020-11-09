<?php

namespace Chopin\Support;

use Composer\Autoload\ClassLoader;
use Laminas\Filter\Word\DashToCamelCase;

abstract class ModulesLoader
{
    public static function buildConfigProvider($dir = 'modules', $skeleton = 'Laminasframework', $prefix = '')
    {
        $osDirIterator = new \DirectoryIterator($dir);
        $configProvider = [];
        $dashToCamelCase = new DashToCamelCase();

        if (strtolower($skeleton) == 'laminasframework' && $dir == 'modules') {
            //預先append Chopin的 ConfigProvider
            $configProvider = self::chopinConfigProvider();
        } else {
            $configProvider = [];
        }



        foreach ($osDirIterator as $dirpath) {
            $filename = $dirpath->getFilename();
            if ($filename == 'chunghsien') {
                continue;
            }
            if ($filename == '.' || $filename == '..') {
                continue;
            }



            $classname = $dashToCamelCase->filter($filename);
            $classname = ucfirst($classname);

            if (strtolower($skeleton) == 'laminasframework') {
                $configProviderClassName = $classname . "\\ConfigProvider";

                if ($prefix) {
                    $configProviderClassName = $prefix . '\\' . $configProviderClassName;
                }
                if (class_exists($configProviderClassName)) {
                    $configProvider[] = $configProviderClassName;
                }
                continue;
            }

            if (strtolower($skeleton) == 'laravel') {
                $laravelProvidersPath = $dir.'/'.$filename. '/src/Providers';
                $laravelDirIterator = new \DirectoryIterator($laravelProvidersPath);
                foreach ($laravelDirIterator as $path) {
                    $filename = $path->getFilename();
                    if ($filename == '.' || $filename == '..') {
                        continue;
                    }
                    $configProviderClassName = $classname . '\\Providers\\' . str_replace('.php', '', $filename);
                    if ($prefix) {
                        $configProviderClassName = $prefix . '\\' . $configProviderClassName;
                    }
                    if (class_exists($configProviderClassName)) {
                        $configProvider[] = $configProviderClassName;
                    }
                }
            }
        }
        return $configProvider;
    }

    protected static function chopinConfigProvider($dir = 'modules/chunghsien')
    {
        $osDir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
        if ( ! is_dir($osDir)) {
            $osDir = 'vendor/chunghsien';
            $osDir = str_replace('/', DIRECTORY_SEPARATOR, $osDir);
        }

        $osDirIterator = new \DirectoryIterator($osDir);
        $dashToCamelCase = new DashToCamelCase();

        $configProviders = [];
        foreach ($osDirIterator as $dirpath) {
            $filename = $dirpath->getFilename();

            if ($filename != '.' && $filename != '..') {
                if ($dirpath->isDir()) {
                    $module = str_replace($osDir . DIRECTORY_SEPARATOR, '', $dirpath->getPathname());
                    $module = str_replace('chopin-', '', $module);
                    $module = $dashToCamelCase->filter($module);
                    $module = ucfirst($module);
                    $configProviderClass = 'Chopin\\' . $module . "\\ConfigProvider";
                    if (class_exists($configProviderClass)) {
                        $configProviders[] = $configProviderClass;
                    }
                }
            }
        }

        return $configProviders;
    }

    public static function registerPsr4(ClassLoader $autoloader, $dir, $prefixName = '')
    {
        $osDir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
        $osDirIterator = new \DirectoryIterator($osDir);
        $dashToCamelCase = new DashToCamelCase();

        foreach ($osDirIterator as $dirpath) {
            $filename = $dirpath->getFilename();

            if ($filename != '.' && $filename != '..') {
                if ($dirpath->isDir()) {
                    $module = str_replace($osDir . DIRECTORY_SEPARATOR, '', $dirpath->getPathname());
                    $module = str_replace('chopin-', '', $module);
                    $module = $dashToCamelCase->filter($module);
                    $module = ucfirst($module);

                    $namespace = $module . "\\";
                    if (strlen($prefixName)) {
                        $namespace = ($prefixName . '\\' . $namespace);
                    }
                    $pathname = $dirpath->getPathname() . DIRECTORY_SEPARATOR . 'src';
                    $autoloader->setPsr4($namespace, $pathname);
                }
            }
        }
        return $autoloader->register(true);
    }
}
