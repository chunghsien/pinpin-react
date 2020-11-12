<?php

namespace Chopin\HttpMessage\Response\AttributeTemplate;

class Success
{
    const CODE = 0;

    protected $message = [];

    protected $data = [];

    public function __construct($message, $data)
    {
        $this->message = $message;
        $this->data = $data;
    }

    public function __toArray()
    {
        return [
            'code' => self::CODE,
            'message' => $this->message,
            'notify' => $this->message,
            'data' => $this->data,
        ];
    }
}
