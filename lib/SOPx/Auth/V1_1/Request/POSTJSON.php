<?php

namespace SOPx\Auth\V1_1\Request;

use \Httpful\Mime;
use \Httpful\Request;
use \SOPx\Auth\V1_1\Util;

class POSTJSON
{
    public static function createRequest(\Net_URL2 $uri, array $params, $app_secret)
    {
        if (!array_key_exists('time', $params) || !$params['time']) {
            throw new \InvalidArgumentException('Missing required parameter "time" in params');
        }
        if (!$app_secret) {
            throw new \InvalidArgumentException('Missing app_secret');
        }

        $data = json_encode($params);
        $sig = Util::createSignature($data, $app_secret);

        $req = Request::post($uri->getURL(), $data, Mime::JSON);
        $req->addHeader('X-Sop-Sig', $sig);
        return $req;
    }
}
