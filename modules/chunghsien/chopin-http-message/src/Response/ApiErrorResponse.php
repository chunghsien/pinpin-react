<?php

namespace Chopin\HttpMessage\Response;

use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Db\ResultSet\ResultSet;

class ApiErrorResponse extends JsonResponse
{
    /**
     *
     * @param int   $code
     * @param mixed $data
     * @param array $message
     * @param array $notify
     * @param number $status
     */
    public function __construct(int $code, $data, array $message, array $notify = [])
    {
        new ResultSet();
        $merge = [
            'code' => $code === 0 ? 1 : $code,
            //'message' => $message,
            'notify' => $notify ? $notify : $message,
            'data' => $data,
        ];
        parent::__construct($merge, 417);
    }
}
