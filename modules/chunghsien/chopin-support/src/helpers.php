<?php

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Db\Sql\Expression;
use Chopin\Support\Log;
use Laminas\Db\TableGateway\Feature\GlobalAdapterFeature;
use Chopin\Support\Registry;
use Laminas\ServiceManager\ServiceManager;
use Intervention\Image\ImageManagerStatic as Image;
use Chopin\LaminasDb\TableGateway\AbstractTableGateway;
use Laminas\I18n\Translator\Translator;
use Laminas\Cache\StorageFactory;

if (! function_exists('config') && is_file('config/config.php')) {
    
    function realFrontendUri($uri='')
    {
        if(!defined('FRONTEND_LOCALE')) {
            define('FRONTEND_LOCALE', '');
        }
        $return = '/'.FRONTEND_LOCALE.'/'.$uri;
        $return = preg_replace('/\/\//', '/', $return);
        return $return;
    }
    
    
    function i18nStaticTranslator($text, $textDomain='default')
    {
        if(defined('BACKEND_LOCALE')) {
            $locale = BACKEND_LOCALE;
            if(!Registry::isRegistered(Translator::class)) {
                $translator = Translator::factory([]);
                Registry::set(Translator::class, $translator);
            }else {
                $translator = Registry::get(Translator::class);
            }
            $allMessages = $translator->getAllMessages($textDomain, $locale);

            if(APP_ENV === 'production') {
                $cache = StorageFactory::factory([
                    'adapter' => [
                        'name' => 'filesystem',
                        'options' => [
                            'dir_level' => 1,
                            'cache_dir' => './storage/cache/app/i18n',
                            'ttl' => 86400 * 7, //one week
                        ],
                    ],
                    'plugins' => [
                        'Serializer',
                    ],
                ]);
                $translator->setCache($cache);
            }
            
            /**
             * @var Translator $translator;
             */
            if(!$allMessages) {
                $file = PROJECT_DIR."/resources/languages/{$locale}/{$textDomain}.php";
                if(is_file($file)) {
                    $translator->addTranslationFile('phpArray', $file, $textDomain);
                    $allMessages = $translator->getAllMessages($textDomain, $locale);
                }
            }
            if(isset($allMessages[$text])) {
                return $allMessages[$text];
            }
        }
        return $text;
    }
    function mergePageJsonConfig($pageJsonConfig)
    {
        $old = Registry::get('page_json_config');
        if (! $old) {
            $old = [];
        }
        $result = array_merge($old, $pageJsonConfig);
        Registry::set('page_json_config', $result);
    }
    
    function moveFolder($src, $dist)
    {
        $it = new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            /**
             * @var \SplFileInfo $file
             */
            if ($file->isDir()) {
                $sourceFolder = $file->getRealPath();
                $distFolder = str_replace(
                    preg_replace('/^\./', '', $src),
                    preg_replace('/^\./', '', $dist),
                    $sourceFolder
                );
                if(!is_dir($distFolder)) {
                    mkdir($distFolder, 0775, true);
                }
            } else {
                $srcFile = $file->getRealPath();
                $distFile = str_replace(
                    preg_replace('/^\./', '', $src),
                    preg_replace('/^\./', '', $dist),
                    $srcFile
                );
                $arr = explode('/', $distFile);
                array_pop($arr);
                $folder = implode('/', $arr);
                if(!is_dir($folder)) {
                    mkdir($folder, 0775, true);
                }
                rename($srcFile, $distFile);
            }
        }
        
    }
    function recursiveRemoveFolder($folder)
    {
        $it = new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($folder);
    }
    
    function copyAdvenced($src, $dst) {
        // open the source directory
        $dir = opendir($src);
        // Make the destination directory if not exist
        @mkdir($dst);
        // Loop through the files in source directory
        foreach (scandir($src) as $file) {
            
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) )
                {
                    
                    // Recursively calling custom copy function
                    // for sub directory
                    copyAdvenced($src . '/' . $file, $dst . '/' . $file);
                    
                }
                else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir); 
    }
    
    function thumbnail($originailPath, $width = 150, $height = null)
    {
        if (! is_file($originailPath)) {
            return preg_replace('/^\.\//', '', $originailPath);
            // throw new \ErrorException('找不到圖片：'.$originailPath);
        }

        $originailPath = preg_replace('/^\.\//', '', $originailPath);
        $matcher = [];
        preg_match('/(?<ext>\.\w{3,})$/', $originailPath, $matcher);
        $ext = $matcher['ext'];
        $size_text = '_w' . (isset($width) ? $width : 'auto') . '_h_' . (isset($height) ? $height : 'auto');

        $thumbPath = str_replace($ext, ('_' . $size_text . '_thumb' . $ext), $originailPath);
        if (is_file($thumbPath)) {
            return '/' . $thumbPath;
        } else {
            $image = Image::make($thumbPath);
            if (is_null($width) && $height > 0) {
                // auto width
                $image->resize(null, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            if (is_null($height) && $width > 0) {
                // auto width
                $image->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            if (is_int($width) && is_int($height)) {
                $image->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            $image->save($thumbPath, 75);
            return '/' . $thumbPath;
        }
    }

    /**
     *
     * @param string $json
     * @param boolean $isHTMLEntities
     * @param boolean $isReplaceHeadAndTailCurlyBrackets
     * @return string
     */
    function json2DataAttr($json, $isHTMLEntities = true, $isReplaceHeadAndTailCurlyBrackets = false)
    {
        if ($isReplaceHeadAndTailCurlyBrackets) {
            $json = preg_replace('/^\{/', '', $json);
            $json = preg_replace('/\}$/', '', $json);
        }

        if ($isHTMLEntities) {
            return htmlentities($json, ENT_QUOTES, 'UTF-8');
        } else {
            $json = str_replace('"', "'", $json);
            return $json;
        }
    }

    /**
     *
     * @param boolean $tailSlash
     * @return string
     */

    /**
     *
     * @param array $server_params
     * @param boolean $tailSlash
     * @return string
     */
    function siteBaseUri($server_params = null, $tailSlash = false)
    {
        $uri = '';
        if (! $server_params) {
            $server_params = $_SERVER;
        }
        $port = intval($server_params['SERVER_PORT']);
        if ($port == 443) {
            $uri = 'https://' . $server_params['SERVER_NAME'];
        } else {
            $uri = 'http://' . $server_params['SERVER_NAME'];
        }
        if ($tailSlash) {
            $uri .= '/';
        }

        return $uri;
    }

    /**
     *
     * @param string|null $key
     * @return NULL|array
     */
    function config($key = null)
    {
        if (is_null($key)) {
            if (preg_match('/^production/i', APP_ENV) && is_file('storage/config-cache.dat')) {
                return unserialize(file_get_contents('storage/config-cache.dat'));
            } else {
                return require 'config/config.php';
            }
        }
        /**
         *
         * @var ServiceManager $serviceManager
         */
        $serviceManager = Registry::get(ServiceManager::class);
        $config = $serviceManager->get('config');
        $keyArr = explode('.', $key);
        if (preg_match('/^production/i', APP_ENV) && ! is_file('storage/config-cache.dat')) {
            file_put_contents('storage/config-cache.dat', serialize($config));
        }
        if (preg_match('/\*$/', $key)) {
            $top = $keyArr[0];
            $allSelectConfig = $config[$top];
            $tmp = [];
            $key = preg_replace('/\*$/', '', $keyArr[1]);
            $key .= '/';
            foreach ($allSelectConfig as $k => $c) {
                if (strpos($k, $key) !== false) {
                    $tmp[$k] = $c;
                }
            }
            return $tmp;
        }
        $result = null;
        foreach ($keyArr as $k) {
            if (! is_null($result)) {
                $result = isset($result[$k]) ? $result[$k] : null;
            } else {
                $result = $config[$k];
            }
        }
        return $result;
    }

    function Json2Props($json, $isHTMLEntities = true)
    {
        $json = preg_replace('/^\{/', '', $json);
        $json = preg_replace('/\}$/', '', $json);
        $json = preg_replace('/\r|\n/', '', $json);
        $json = preg_replace('/\s{4}/', '', $json);
        if ($isHTMLEntities) {
            return htmlentities($json, ENT_QUOTES, 'UTF-8');
        } else {
            $json = str_replace('"', "'", $json);
            return $json;
        }
    }

    function isApiRequest($options = [])
    {
        $request = ServerRequestFactory::fromGlobals();
        $verfiCount = 0;
        // extract($options);
        if (isset($options['xRequestWith'])) {
            $xRequestWith = $options['xRequestWith'];
        } else {
            $xRequestWith = 'XMLHttpRequest';
        }
        if (isset($options['contentType'])) {
            $contentType = $options['contentType'];
        } else {
            $contentType = 'application/json';
        }

        if (isset($options['accept'])) {
            $accept = $options['accept'];
        } else {
            $accept = '';
        }

        if ($request->hasHeader('X-Requested-With')) {
            $verfiCount ++;
            $headerXRequestWith = implode('', $request->getHeader('X-Requested-With'));
            if ($headerXRequestWith != $xRequestWith) {
                return false;
            }
        }
        if ($request->hasHeader('Content-Type')) {
            $verfiCount ++;
            $headerContentType = implode('', $request->getHeader('Content-Type'));

            if (0 !== strpos($headerContentType, $contentType)) {
                return false;
            }
        }

        if ($request->hasHeader('Accept')) {
            $verfiCount ++;
            $headerAccept = implode('', $request->getHeader('Accept'));
            if (($headerAccept != $accept) && $accept != '*/*') {
                return false;
            }
        }

        $serverParams = $request->getServerParams();

        if (isset($serverParams['HTTP_X_REQUESTED_WITH'])) {
            $verfiCount ++;
            $httpXRequestedWithForServerParam = $serverParams['HTTP_X_REQUESTED_WITH'];
            if ($httpXRequestedWithForServerParam != $xRequestWith) {
                return false;
            }
        }

        if (! $verfiCount) {
            return false;
        }

        return true;
    }

    function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     *
     * @param mixed $var
     * @param array $options
     */
    function debug($var, $options = [])
    {
        if (isset($options['log']) && $options['log']) {
            $output = var_export($var, true);
            $output = preg_replace("/\n$/", '', $output);
            logger()->debug($output);
            return;
        }
        $varOutputType = isset($options['output_type']) ? $options['output_type'] : '';
        $varOutputType = strtolower($varOutputType);

        if (isset($options['profile'])) {
            if (! is_dir('storage/debug')) {
                mkdir('storage/debug', 0755, true);
            }
            $file = 'storage/debug/debug_profile.dat';
            file_put_contents($file, var_export($var, true) . PHP_EOL, FILE_APPEND);
            ob_end_clean();
            return;
        }
        if (empty($options['is_console_display']) || $options['is_console_display'] == false) {
            if($varOutputType != 'json') {
                echo '<pre>';
            }
        }
        switch ($varOutputType) {
            case 'export':
                var_export($var);
                break;
            
            case 'json':
                //header('Content-Type: application/json; charset=utf-8');
                //echo json_encode($var, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                break;
            
            default: // var_dump
                var_dump($var);
                break;
        }
        if (isset($options['lineno'])) {
            if($varOutputType != 'json') {
                echo ('Line: ' . $options['lineno'] . PHP_EOL);
            }else {
                $var['Line'] = $options['lineno'];
            }
        }

        if (isset($options['filename'])) {
            if($varOutputType != 'json') {
                echo ('File: ' . $options['filename'] . PHP_EOL);
            }else {
                $var['File'] = $options['filename'];
            }
        }
        if (empty($options['is_console_display']) || $options['is_console_display'] == false) {
            if($varOutputType != 'json') {
                echo '</pre>';
            }
        }
        
        if($varOutputType == 'json') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($var, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit();
        }
        
        if (empty($options['isContinue'])) {
            exit();
        }
    }

    function PDOBindParamRaw($raw)
    {
        return new Expression($raw);
    }

    /**
     *
     * @return \Laminas\Log\Logger
     */
    function logger()
    {
        return Log::log();
    }
    
    function loggerException($e) {
        $errorArr = [
            '',
            'code:    '.$e->getCode(),
            'file:    '.$e->getFile(),
            'line:    '.$e->getLine(),
            'message: '.$e->getMessage(),
            'trace:   '.$e->getTraceAsString(),
            
        ];
        logger()->info(implode(PHP_EOL, $errorArr));
    }
    
    /**
     *
     * @param string $tablegatewayClassname
     * @param string $valueField
     * @param string $lableField
     * @param array $predicateParams
     */
    function getOptions($tablegatewayClassname, $valueField, $lableField, $dataAttrs = [], $predicateParams = [])
    {
        $reflection = new ReflectionClass($tablegatewayClassname);

        /**
         *
         * @var AbstractTableGateway $tableGateway
         */
        $tableGateway = $reflection->newInstance(GlobalAdapterFeature::getStaticAdapter());
        return $tableGateway->getOptions($valueField, $lableField, $dataAttrs, $predicateParams);
    }

    function getConfigPrefixNoMVC($headReplace = '')
    {
        $scriptName = preg_replace('/^\//', '', $_SERVER["SCRIPT_NAME"]);
        $scriptName = preg_replace('/\.php$/', '', $scriptName);
        if ($headReplace) {
            $scriptName = preg_replace('/^\w+\//', $headReplace . '/', $scriptName);
        }
        return $scriptName;
    }

    /**
     *
     * @param
     *            $search
     * @return array
     */
    function fileCacheBlurSearch($search)
    {
        $pattern = sprintf('storage/cache/zfcache-*/zfcache-%s*.dat', $search);
        return glob($pattern, GLOB_NOSORT);
    }
    
    function traceVarsProgress($var)
    {
        if(preg_match('/^(192|128)\./', $_SERVER['SERVER_ADDR']))
        {
            //REQUEST_TIME_FLOAT
            $folder = "./storage/log/vars/".REQUEST_TIME_FLOAT;
            if(!is_dir($folder)) {
                mkdir($folder, 0644, true);
            }
            $filename = microtime(true).'.log';
            $path = "{$folder}/$filename";
            file_put_contents($path, var_export($var, true));
        }
    }
    /**
     *
     * @param \Throwable $e
     * @param boolean $die
     */
    function TryCatchTransToLog($e, $die = false)
    {
        $message = iconv('UTF-8', 'BIG5', $e->getMessage());
        $errorMessage = 
<<< ERROR_MESSAGE
Code:            {$e->getCode()}
File:            {$e->getFile()}
Line:            {$e->getLine()}
Message:         {$message}
Previous:        {$e->getPrevious()}
Trace as string:
{$e->getTraceAsString()}
ERROR_MESSAGE;

        if ($die) {
            echo '<pre>';
            echo $errorMessage;
            echo '</pre>';
            exit();
        }
        logger()->err($errorMessage);
    }
}
