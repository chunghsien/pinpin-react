<?php

namespace Chopin\HttpMessage\Response;

use Laminas\Diactoros\Response\JsonResponse;

class ApiSuccessResponse extends JsonResponse
{
    
    static public $is_json_numeric_check = true;
    
    /**
     * @paream int $code
     * @param mixed $data
     * @param array $message
     * @param array $notify
     * @param number $status
     */
    public function __construct(int $code, $data, array $message = [], array $notify = [])
    {
        $merge = [
            'code' => $code !== 0  ? 0 : $code ,
            'message' => $message,
            'notify' => $notify ? $notify : $message,
            'data' => $data,
        ];
        if(self::$is_json_numeric_check === true) {
            parent::__construct($merge, 200, [], JSON_NUMERIC_CHECK);
        }else {
            parent::__construct($merge, 200, []);
        }
        
    }
}
