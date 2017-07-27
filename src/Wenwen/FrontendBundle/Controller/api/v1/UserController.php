<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\RestAuthenticatedController;

class UserController extends RestAuthenticatedController
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

        $smsService = $this->get('app.sms_service');
        if (!$smsService->sendSms($token)) {
            return $this->view([
                'status' => 'success',
                'message' => 'Failed to send sms',
            ], 400);
        }

        return $this->view([
            'status' => 'success',
            'data' => [
                'mobile_number' => $mobile_number,
                'mobile_token' => $token,
                'expires_at' => time() + $token_live_seconds,
            ]
        ], 201);
    }

    private function generateToken($length = 6) {
        $randStr = str_shuffle('1234567890');
        $rand = substr($randStr, 0, $length);
        return $rand;
    }
}