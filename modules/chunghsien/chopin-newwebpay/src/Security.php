<?php

namespace Chopin\Newwebpay;

abstract class Security
{

    /**
     *
     * @param array $paramters
     * @param string $hashKey
     * @param string $hashIV
     * @return string
     */
    public static function create_mpg_aes_encrypt($parameters, $hashKey, $hashIV)
    {
        $return_str = '';
        $return_str = http_build_query($parameters);
        $padding_str = self::addpadding($return_str);
        return trim(bin2hex(openssl_encrypt($padding_str, 'aes-256-cbc', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV)));
    }

    /**
     *
     * @param string $string
     * @param int $blocksize
     * @return string
     */
    protected static function addpadding($string, $blocksize = 32)
    {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    /**
     *
     * @param string $parameters
     * @param string $hashKey
     * @param string $hashIV
     * @return boolean|string
     */
    public static function create_aes_decrypt($parameters, $hashKey, $hashIV)
    {
        $strippadding_str = self::strippadding(openssl_decrypt(hex2bin($parameters), 'AES-256-CBC', $hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $hashIV));
        return $strippadding_str;
    }

    public static function createSha256Token($hashKey, $hashIV, $aesToken)
    {
        $token = 'HashKey='.$hashKey.'&'.$aesToken.'&HashIV='.$hashIV;
        return strtoupper(hash('sha256', $token));
    }
    /**
     *
     * @param string $string
     * @return string|boolean
     */
    protected static function strippadding($string)
    {
        $slast = ord(substr($string, - 1));
        $slastc = chr($slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }
}
