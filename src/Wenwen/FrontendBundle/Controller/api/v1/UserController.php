<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\MyFOSRestController;

class UserController extends MyFOSRestController
{
    /**
     * @Rest\Post("/users/sms-token")
     */
    public function smsTokenAction(Request $request) {
        $mobile_number = $request->get('mobile_number');

        $token = $this->generateToken(4);

        $redis = $this->get('snc_redis.default');
        $redis->set($mobile_number, $token);

        $token_live_seconds = 600;
        $redis->expire($mobile_number, $token_live_seconds);

        $data = [];
        $smsService = $this->get('app.sms_service');
        if ($smsService->sendSms($token)) {
            $data['status'] = 'success';
            $data['data'] = [
                'mobile_number' => $mobile_number,
                'mobile_token' => $token,
                'expires_at' => time() + $token_live_seconds,
            ];
        } else {
            $data['status'] = 'error';
            $data['message'] = '发送短信失败';
        }

        return $this->view($data, 201);
    }

    private function generateToken($length = 6) {
        $randStr = str_shuffle('1234567890');
        $rand = substr($randStr, 0, $length);
        return $rand;
    }
}