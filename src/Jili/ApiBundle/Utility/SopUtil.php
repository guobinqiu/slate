<?php

namespace Jili\ApiBundle\Utility;

/**
 *
 */
class SopUtil
{
    static $SIG_VALID_FOR_SEC = 600;

    // Valid for 10 min by default
    public static function createStringFromArray($params)
    {
        ksort($params);
        $query = array ();
        foreach ($params as $key => $value) {
            if (!is_scalar($value)) {
                throw new \InvalidArgumentException('Non-scalar value in parameter: ' . $key);
            }
            if (!preg_match('/^sop_/', $key)) {
                $query[] = $key . '=' . $value;
            }
        }
        return implode('&', $query);
    }

    public static function createSignature($params, $app_secret)
    {
        $data_string = '';
        if (is_array($params)) {
            $data_string = self::createStringFromArray($params);
        } else if (is_scalar($params)) {
            $data_string = $params;
        } else {
            throw new \InvalidArgumentException('Non-compatible type provided to $params');
        }
        return hash_hmac('sha256', $data_string, $app_secret);
    }

    public static function isSignatureValid($sig, $params, $app_secret, $time = null)
    {
        if (!$sig || !$params || !$app_secret) {
            return false;
        }
        if (!$time) {
            $time = time();
        }
        $req_time = 0;
        if (is_array($params) && array_key_exists('time', $params)) {
            $req_time = $params['time'];
        } else if (is_scalar($params)) {
            $data = json_decode($params, true); // Assume it's a JSON
            if (json_last_error()) {
                throw new \InvalidArgumentException('Malformed string was given where JSON was expected');
            }
            if (array_key_exists('time', $data)) {
                $req_time = $data['time'];
            }
        }
        // `time` is mandatory
        if (!$req_time) {
            return false;
        }
        // Too old
        if ($req_time < ($time - self::$SIG_VALID_FOR_SEC)) {
            return false;
        }
        // Too new
        if ($req_time > ($time + self::$SIG_VALID_FOR_SEC)) {
            return false;
        }
        return $sig === self::createSignature($params, $app_secret);
    }

    public static function getJsopURL($sop_params,$app_sop_host)
    {
        $required = array('app_id', 'app_mid', 'sig', 'time', 'sop_callback');

        if(count(array_intersect_key(array_flip($required), $sop_params)) !== count($required)) {
            throw new Exception("Insuffucient parameter", 1);
        }

        $query = http_build_query(array(
            'app_id'       => $sop_params['app_id'],
            'app_mid'      => $sop_params['app_mid'],
            'sig'          => $sop_params['sig'],
            'time'         => $sop_params['time'],
            'sop_callback' => $sop_params['sop_callback'],
        ));
        $url = 'https://' . $app_sop_host . '/api/v1_1/surveys/js?' . $query;
        return $url;
    }
}
