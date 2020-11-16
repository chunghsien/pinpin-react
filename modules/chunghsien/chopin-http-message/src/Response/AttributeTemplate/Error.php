<?php

namespace Chopin\HttpMessage\Response\AttributeTemplate;

use Laminas\Validator\AbstractValidator;

class Error
{
    const CODE = 1;

    protected $message = [];

    protected $data = [];

    protected $htmlParagraph = '';

    /**
     *
     * @param array $message
     * @param array $data
     * @param string $htmlParagraph
     */
    public function __construct($message, $data, $htmlParagraph = 'br')
    {
        $this->message = $message;
        $this->data = $data;
        $this->htmlParagraph =$htmlParagraph;
    }

    public function __toArray()
    {
        return [
            'code' => self::CODE,
            'message' => $this->message,
            'notify' => $this->messageToNotify(),
            'data' => $this->data,
        ];
    }

    /**
     *
     * @param string $htmlParagraph
     * @return string
     */
    protected function messageToNotify()
    {
        $message = $this->message;
        $translator = AbstractValidator::getDefaultTranslator();
        $alertifyMessage = '';
        $htmlParagraph = $this->htmlParagraph;
        if (is_array($message) || $message instanceof \Iterator) {
            foreach ($message as $key => $value) {
                if (is_int($key)) {
                    $paragraph = $translator->translate($value).'<br>';
                } else {
                    if (preg_match('/br/i', $htmlParagraph)) {
                        $paragraph = $translator->translate($key).': '.$translator->translate($value).'<br>';
                    }
                    if (preg_match('/p/i', $htmlParagraph)) {
                        $paragraph = '<p>'.$translator->translate($key).': '.$translator->translate($value).'</p>';
                    }
                }
                $alertifyMessage.= $paragraph;
            }
        } else {
            $alertifyMessage = $message;
        }
        return $alertifyMessage;
    }
}
