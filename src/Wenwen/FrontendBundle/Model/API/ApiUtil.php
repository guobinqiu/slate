<?php

namespace Wenwen\FrontendBundle\Model\API;

use JMS\Serializer\SerializerBuilder;

class ApiUtil
{
    const HTTP_HEADER_APP_ACCESS_TOKEN = 'X-App-Access-Token';
    const HTTP_HEADER_TIMESTAMP = 'X-Timestamp';
    const HTTP_HEADER_NONCE = 'X-Nonce';
    const HTTP_HEADER_USER_ACCESS_TOKEN = 'X-User-Access-Token';

    const ALGO = 'sha256'; // Can be one of md5, sha1, ...
    const REPLAY_ATTACK_LIVE_SECONDS = 600; //10min
    const MOBILE_TOKEN_LIVE_SECONDS = 600; //10min
    const USER_ACCESS_TOKEN_LIVE_SECONDS = 1800; //30min

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
        return json_decode(self::objectToJSON($obj, 'json'), true);
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

    public static function objectToJSON($obj) {
        $serializer = SerializerBuilder::create()->build();
        return $serializer->serialize($obj, 'json');
    }
}
