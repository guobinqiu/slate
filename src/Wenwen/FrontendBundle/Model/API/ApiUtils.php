<?php

namespace Wenwen\FrontendBundle\Model\API;

class ApiUtils
{
    const HTTP_HEADER_AUTHORIZATION = 'X-Authorization';
    const HTTP_HEADER_TIMESTAMP = 'X-Timestamp';
    const HTTP_HEADER_NONCE = 'X-Nonce';
    const HTTP_HEADER_LOGIN_TOKEN = 'X-login-token';

    const HMAC_ALGO = 'sha256'; // Can be one of md5, sha1, ...
    const LIVE_TIME = 300; // 5min

    public static function formatSuccess($data) {
        return [
            'status' => 'success',
            'data' => $data,
        ];
    }

    public static function formatError($data) {
        $response['status'] = 'error';

        if (is_array($data)) {
            $response['messages'] = $data;
        } else {
            $response['message'] = $data;
        }

        return $response;
    }

    public static function array_to_object($arr) {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)self::array_to_object($v);
            }
        }

        return (object)$arr;
    }

    public static function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)self::object_to_array($v);
            }
        }

        return $obj;
    }

    public static function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public static function urlsafe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

}
