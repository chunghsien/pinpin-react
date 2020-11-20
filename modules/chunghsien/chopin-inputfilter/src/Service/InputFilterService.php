<?php

namespace Chopin\Inputfilter\Service;

use Psr\Http\Message\ServerRequestInterface;
use Laminas\InputFilter\InputFilterPluginManager;
use Laminas\Http\PhpEnvironment;
use Laminas\Filter\File\RenameUpload;
use Chopin\HttpMessage\Response\AttributeTemplate\Error;
use Chopin\HttpMessage\Response\AttributeTemplate\Success;
//use App\Service\ServiceTrait;
use Chopin\Inputfilter\Patterns\KeyValuePattern;

class InputFilterService
{
    //use ServiceTrait;

    /**
     *
     * @var InputFilterPluginManager
     */
    private $inputFilterManager;

    /**
     *
     * @var ServerRequestInterface
     */
    private $request;

    /**
     *
     * @var RenameUpload
     */
    private $renameUpload;

    private $renameUploadDefaultConfig = [
        //'target' => './storage/uploads/',
        'randomize' => true,
        'use_upload_extension' => true,
    ];

    // const VIRTUAL_RESTFUL_VARIABLE = 'x-http-method-override';

    /**
     *
     * @var PhpEnvironment\Request|ServerRequestInterface
     */
    private $phpEnvironmentRequest;

    public function __get($name)
    {
        if (strtolower($name) == 'request') {
            return $this->request;
        }
        return null;
    }

    public function __construct(InputFilterPluginManager $inputFilterManager, ServerRequestInterface $request, $reanmeUploadOptions = [])
    {
        $this->request = $request;
        $this->inputFilterManager = $inputFilterManager;
        $this->request = $request;
        $renameUploadConfig = array_merge($this->renameUploadDefaultConfig, $reanmeUploadOptions);
        if (empty($renameUploadConfig['target'])) {
            $renameUploadConfig['target'] = './storage/uploads/'.date('Ymd');
        }
        if ( ! is_dir($renameUploadConfig['target'])) {
            mkdir($renameUploadConfig['target'], 0755, true);
        }
        $this->renameUpload = new RenameUpload($renameUploadConfig);
        $this->renameUpload->setOverwrite(false);
        if (floatval(PHP_VERSION) < 7.0) {
            $this->phpEnvironmentRequest = new PhpEnvironment\Request();
        } else {
            $this->phpEnvironmentRequest = $request;
        }
    }

    public function getUploadFiles()
    {
        $uploads = null;
        if (floatval(PHP_VERSION) < 7.0) {
            // new \Laminas\Stdlib\Parameters;
            $uploads = $this->phpEnvironmentRequest->getFiles()->toArray();
        } else {
            $uploads =  $this->phpEnvironmentRequest->getUploadedFiles();
        }
        return $uploads;
    }

    public function withParseBody($paseBody)
    {
        $this->request = $this->request->withParsedBody($paseBody);
    }

    public function withUploadFiles($uploadedFiles)
    {
        if (floatval(PHP_VERSION) < 7.0) {
            $this->phpEnvironmentRequest = $this->phpEnvironmentRequest->setFiles(new \Laminas\Stdlib\Parameters($uploadedFiles));
        } else {
            $this->phpEnvironmentRequest = $this->phpEnvironmentRequest->withUploadedFiles($uploadedFiles);
        }
    }

    /**
     *
     * @param boolean $isMessageImplode
     * @return string[]|array[]
     */
    public function run($isMessageImplode = true)
    {
        $configs = config('input_filter_specs.'.$this->getPrefixKey().'*');
        $keys = array_keys($configs);
        $datas = $this->request->getParsedBody();
        $http_request_method = isset($datas['x-http-method-override']) ? $datas['x-http-method-override'] : '';
        if ( ! $http_request_method && $this->request->hasHeader('X-Http-Method-Override')) {
            $http_request_method = implode('', $this->request->getHeader('X-Http-Method-Override'));
        }
        if ( ! $http_request_method) {
            $http_request_method = strtolower($this->request->getMethod());
        }

        $final_data = [];
        $filesData = $this->getUploadFiles();
        if ($filesData) {
            foreach ($filesData as $field => $value) {
                $matches = [];
                preg_match('/(?<table>\w+)\-(?<column>\w+)$/', $field, $matches);
                $table = $matches['table'];
                if (preg_match('/^with\-/', $field)) {
                    $table = 'with-'.$table;
                }
                $column = $matches['column'];
                if (empty($final_data[$table])) {
                    $final_data[$table] = [];
                }
                $final_data[$table][$column] = $value;
            }
        }

        foreach ($datas as $field => $value) {
            $matches = [];
            preg_match('/(?<table>\w+)\-(?<column>\w+)$/', $field, $matches);

            if ($matches) {
                $table = $matches['table'];
            }

            if (preg_match('/^with\-/', $field)) {
                $table = 'with-'.$table;
            }
            $column = $matches['column'];
            if (empty($final_data[$table])) {
                $final_data[$table] = [];
            }
            $final_data[$table][$column] = $value;
            //debug($matches);
        }
        foreach ($keys as $key) {
            /**
             *
             * @var \Laminas\InputFilter\InputFilter $inputFilter
             */
            $inputFilter = $this->inputFilterManager->get($key);

            $options = config('input_filter_specs.' . $key);
            $matches = [];
            preg_match('/(?<table>\w+)$/', $key, $matches);
            $table = $matches['table'];

            if (preg_match('/with\-\w+$/', $key)) {
                $table = 'with-'.$table;
            }

            if (isset($final_data[$table]['id']) && $final_data[$table]['id']) {
                $tdata = $final_data[$table]['id'];
                if (is_array($tdata)) {
                    $verifyCount = 0;
                    $__columns = array_keys($final_data[$table]);
                    $allowColumns = ['path', 'file', 'photo', 'image', 'avater', 'banner'];
                    $tableVerify = array_intersect($__columns, $allowColumns);
                    foreach ($tdata as $t) {
                        if ($tableVerify) {
                            //需要整批陣列都符合。
                            if (intval($t)) {
                                $verifyCount++;
                            }
                        }
                    }
                    if ($verifyCount == count($tdata)) {
                        $http_request_method = 'put';
                    } else {
                        $http_request_method = 'post';
                    }
                } else {
                    if (intval($final_data[$table]['id'])) {
                        $http_request_method = 'put';
                    } else {
                        $http_request_method = 'post';
                    }
                }
            } else {
                $http_request_method = 'post';
            }
            foreach ($options as $option) {
                if (empty($option['required-confirm'])) {
                    continue;
                }
                $name = $option['name'];
                $confirm = $option['required-confirm'];
                $input = $inputFilter->get($name);
                if (isset($confirm[$http_request_method])) {
                    $verify = $confirm[$http_request_method];
                    $input->setRequired($verify);
                }

                $inputFilter->add($input, $name);
            }
            $_matches = [];
            preg_match('/(?P<table>\w+)$/', $key, $_matches);
            $dataTableName = $_matches['table'];
            if (preg_match('/with\-\w+$/', $key)) {
                $dataTableName = 'with-'.$dataTableName;
            }

            //處理陣列資料型態
            if ($this->isValueArrayType($final_data[$dataTableName])) {
                foreach ($final_data[$dataTableName] as $filed_key => $vs) {
                    $input = $inputFilter->get($filed_key);
                    foreach ($vs as $idx => $v) {
                        $input->setValue($v);
                        if ($input->isRequired()) {
                            if ( ! $input->isValid()) {
                                $name = $dataTableName.'-'.$filed_key.sprintf('[%d]', $idx);
                                $message = implode(PHP_EOL, $input->getMessages());
                                $message = nl2br($message);
                                return (new Error([
                                    $name => $message,
                                ], []))->__toArray();
                            }
                        }
                    }
                    $final_data[$dataTableName][$filed_key] = $vs;
                }
                $valid = true;
            } else {
                // 修正 Fileinput isRequired == false 還會繼續檢查的問題
                foreach ($inputFilter->getInputs() as $ikey => $input) {
                    if ($input instanceof \Laminas\InputFilter\FileInput) {
                        if ($input->isRequired() === false) {
                            $inputFilter->remove($ikey);
                        }
                    }
                }
                $inputFilter->setData($final_data[$dataTableName]);
                $valid = $inputFilter->isValid();
            }
            if ( ! $valid) {
                $error = $inputFilter->getMessages();
                if ($isMessageImplode) {
                    foreach ($error as $k => $values) {
                        if (count($values) > 1) {
                            $str = implode(PHP_EOL, $values);
                        } else {
                            $str = implode('', $values);
                        }
                        $str = nl2br($str);
                        $name = $dataTableName.'-'.$k;
                        $error[$name] = $str;
                        unset($error[$k]);
                    }
                }
                return (new Error($error, []))->__toArray();
            }
            if (empty($final_data[$dataTableName])) {
                $final_data[$dataTableName] = [];
                //$final_data[$dataTableName] = $inputFilter->getValues();
                //var_export($final_data[$dataTableName]);
            }

            foreach ($final_data[$dataTableName] as &$value) {
                if (isset($value['tmp_name']) && isset($value['error'])) {
                    $value = $this->renameUpload->filter($value);
                    $value = $value['tmp_name'];
                    continue;
                }

                if (is_array($value)) {
                    foreach ($value as &$f) {
                        if (isset($f['tmp_name']) && isset($f['error'])) {
                            $f = $this->renameUpload->filter($f);
                            $f = $f['tmp_name'];
                            continue;
                        }
                    }
                }
            }

            $keys = array_keys($final_data);
            foreach ($keys as $keyName) {
                if (preg_match('/^'.$dataTableName.'\-/', $keyName)) {
                    unset($final_data[$keyName]);
                }
            }
            if (isset($final_data[""])) {
                unset($final_data[""]);
            }
        }
        $final_data = (new KeyValuePattern())->reBuild($final_data);
        return (new Success('資料驗證正確', $final_data))->__toArray();
    }

    /**
     *
     * @param array $datas
     * @return boolean
     */
    protected function isValueArrayType($datas)
    {
        if (is_array($datas) || $datas instanceof \Traversable) {
            foreach ($datas as $key => $value) {
                return is_int($key) && is_array($value);
            }
        }
    }
}
