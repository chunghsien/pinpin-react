<?php

namespace Chopin\HttpMessage\Response;

use Laminas\Diactoros\Response\JsonResponse;

class ApiSuccessResponse extends JsonResponse
{
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
        parent::__construct($merge, 200, [], JSON_NUMERIC_CHECK);
    }
}
