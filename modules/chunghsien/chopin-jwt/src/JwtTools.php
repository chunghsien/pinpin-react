<?php

namespace Chopin\Jwt;

abstract class JwtTools
{

    /**
     *
     * @param mixed $data
     * @param number $expPlus
     * @return mixed
     */
    public static function buildPayload($data, $expPlus = 28800)
    {
        $iat = strtotime('now');
        return [
            'iss' => $_SERVER['SERVER_NAME'],
            'aud' => $_SERVER['REMOTE_ADDR'],
            'iat' => $iat,
            'nbf' => $iat - 1,
            'exp' => ($iat + $expPlus),
            'data' => $data,
        ];
    }

    /**
     *
     * @param mixed $payload
     * @return boolean
     */
    public static function verify($payload)
    {
        $iat = strtotime('now');
        if ( ! isset($payload)) {
            return false;
        }

        if ($payload->iss != $_SERVER['SERVER_NAME']) {
            return false;
        }

        if ($payload->aud != $_SERVER['REMOTE_ADDR']) {
            return false;
        }

        if ($payload->nbf >  $iat) {
            return false;
        }

        if ($payload->exp <  $iat) {
            return false;
        }


        return true;
    }
}
