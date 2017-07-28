<?php

namespace Wenwen\FrontendBundle\Model\API;

use JMS\Serializer\SerializerBuilder;

class ApiUtils
{
    const HTTP_HEADER_AUTHORIZATION = 'X-Authorization';
    const HTTP_HEADER_TIMESTAMP = 'X-Timestamp';
    const HTTP_HEADER_NONCE = 'X-Nonce';
    const HTTP_HEADER_LOGIN_TOKEN = 'X-login-token';

    const HMAC_ALGO = 'sha256'; // Can be one of md5, sha1, ...
    const REPLAY_ATTACK_LIVE_SECONDS = 300;
    const MOBILE_TOKEN_LIVE_SECONDS = 600;

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

    public static function objectToArray($obj) {
        $serializer = SerializerBuilder::create()->build();
        return json_decode($serializer->serialize($obj, 'json'), true);
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
