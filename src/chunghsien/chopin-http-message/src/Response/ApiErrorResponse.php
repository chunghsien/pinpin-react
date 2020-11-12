<?php

namespace Chopin\HttpMessage\Response;

use Laminas\Diactoros\Response\JsonResponse;

class ApiErrorResponse extends JsonResponse
{
    /**
     *
     * @param int   $code
     * @param array $data
     * @param array $message
     * @param array $notify
     * @param number $status
     */
    public function __construct(int $code, array $data, array $message, array $notify = [])
    {
        $merge = [
            'code' => $code === 0 ? 1 : $code,
            //'message' => $message,
            'notify' => $notify ? $notify : $message,
            'data' => $data,
        ];
        parent::__construct($merge, 417);
    }
}
