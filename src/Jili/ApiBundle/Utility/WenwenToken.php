<?php
namespace Jili\ApiBundle\Utility;

/**
 *
 **/
class WenwenToken {

    public static $SECRET_KEY = 'ADF93768CF';
    public static $ONE_TIME_TOKEN_LENGTH = 32;
    public static $SIG_VALID_FOR_SEC = 32;

    public static function getUniqueToken($value) {
        $seed = self :: $SECRET_KEY;
        $hash = sha1($value . $seed);
        for ($i = 0; $i < 5; $i++) {
            $hash = sha1($hash);
        }
        return $hash;
    }

    public static function createStringFromArray($params) {
        ksort($params);
        $query = array ();
        foreach ($params as $key => $value) {
            if (!is_scalar($value)) {
                throw new \ InvalidArgumentException('Non-scalar value in parameter: ' . $key);
            }
            $query[] = $key . '=' . $value;
        }
        return implode('&', $query);
    }

    public static function createSignature($params, $secret_key) {
        if (!is_array($params)) {
            throw new \ InvalidArgumentException('Non-compatible type provided to $params');
        }
        $data_string = self :: createStringFromArray($params);
        return hash_hmac('sha256', $data_string, $secret_key);
    }

    public static function isSignatureValid($sig, $params, $secret_key, $time = null) {
        if (!$sig || !$params || !$secret_key) {
            return false;
        }

        if (is_null($time)) {
            $time = time();
        }

        $req_time = array_key_exists('time', $params) ? $params['time'] : 0;
        if (!$req_time) {
            return false;
        }
        // Too old
        if ($req_time < ($time -self :: $SIG_VALID_FOR_SEC)) {
            return false;
        }
        // Too new
        if ($req_time > ($time +self :: $SIG_VALID_FOR_SEC)) {
            return false;
        }

        return $sig === self :: createSignature($params, $secret_key);
    }

    public static function generateOnetimeToken() {
        return bin2hex(openssl_random_pseudo_bytes(self :: $ONE_TIME_TOKEN_LENGTH, $cstrong));
    }
}
