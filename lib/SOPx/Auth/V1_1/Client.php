<?php

namespace SOPx\Auth\V1_1;

use \Net_URL2;
use \SOPx\Auth\V1_1\Request\GET;
use \SOPx\Auth\V1_1\Request\POST;
use \SOPx\Auth\V1_1\Request\POSTJSON;

class Client
{
    protected $app_id, $app_secret;

    public function __construct($app_id, $app_secret, $time = null)
    {
        if (!$app_id) {
            throw new \InvalidArgumentException('Missing required parameter: app_id');
        }
        if (!$app_secret) {
            throw new \InvalidArgumentException('Missing required parameter: app_secret');
        }
        if (!$time) {
            $time = time();
        }

        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
        $this->time = $time;
    }

    public function getAppId() { return $this->app_id; }
    public function getAppSecret() { return $this->app_secret; }
    public function getTime() { return $this->time; }

    public function createRequest($method, $uri, $params)
    {
        if (!is_object($uri)) {
            $uri = new \Net_URL2($uri);
        }
        $params['time'] = $this->getTime();

        switch ($method) {
            case 'GET':
                $req = GET::createRequest($uri, $params, $this->getAppSecret());
                break;
            case 'POST':
                $req = POST::createRequest($uri, $params, $this->getAppSecret());
                break;
            case 'POSTJSON':
                $req = POSTJSON::createRequest($uri, $params, $this->getAppSecret());
                break;
            default:
                throw new \InvalidArgumentException('Cannot handle method: '. $method);
        }
        return $req;
    }

    public function verifySignature($sig, $params)
    {
        $result = array();
        $result['status'] = false;
        $result['msg'] = '';
        try{
            Util::isSignatureValid(
                $sig,
                $params,
                $this->getAppSecret(),
                $this->getTime()
            );
        } catch (\Exception $e){
            $result['status'] = false;
            $result['msg'] = $e->getMessage();
            return $result;
        }

        $result['status'] = true;
        return $result;
    }
}
