<?php

namespace Chopin\LaminasDb\Services\Traits;

use Laminas\InputFilter\Factory;
use Laminas\I18n\Translator\Translator;
use Laminas\Validator\AbstractValidator;
use Laminas\InputFilter\BaseInputFilter;
use Chopin\I18n\LaminasValitorTranslator;

trait InputFilterTrait
{

    /**
     *
     * @var Translator
     */
    protected $validatorTranslator;



    /**
     *
     * @var BaseInputFilter[]
     */
    protected $inputFilterContainer;

    /**
     *
     * @var Factory
     */
    protected $inputFilterfactory;

    protected function initInputFilterFactory()
    {
        $this->inputFilterFactory = new Factory();
    }

    public function addInputFilter($path, $table)
    {
        $filterArr = require_once $path;
        $inputFilter = $this->inputFilterFactory->createInputFilter($filterArr[$table]);
        $this->inputFilterContainer[$table] = $inputFilter;
    }

    /**
     *
     * @param array $inputFilterSpecification
     * @param string $table
     */
    public function createInputFilter($inputFilterSpecification, $table)
    {
        $inputFilter = $this->inputFilterFactory->createInputFilter($inputFilterSpecification[$table]);
        $this->inputFilterContainer[$table] = $inputFilter;
    }


    /**
     *
     * @param array $attributes
     * @param string $table
     * @param string $locale
     * @return array|string[][]
     */
    public function getErrorMessages($attributes, $table, $locale='en')
    {
        $this->validatorTranslator->setLocale($locale);
        $inputFilter = $this->inputFilterContainer[$table];
        $inputFilter->setData($attributes);
        $inputFilter->isValid();
        $messages = $inputFilter->getMessages();

        foreach ($messages as &$message) {
            if (is_array($message)) {
                $message = array_values($message);
            }
        }
        $newMessage = [];
        foreach ($messages as $column => $value) {
            $alias = $table.'_'.$column;
            $newMessage[$alias] = $value;
        }
        unset($messages);
        return $newMessage;
    }

    protected function initTranslator($options = [])
    {
        $options = array_merge_recursive(config('translator'), $options);
        $this->validatorTranslator = LaminasValitorTranslator::factory($options);
        AbstractValidator::setDefaultTranslator($this->validatorTranslator);
    }
}
