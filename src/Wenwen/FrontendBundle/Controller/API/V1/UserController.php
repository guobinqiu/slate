<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\RestAuthenticatedController;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Form\API\V1\LoginType;
use Wenwen\FrontendBundle\Model\API\ApiUtils;
use Wenwen\FrontendBundle\Model\API\Status;
use Wenwen\FrontendBundle\Annotation\API\Login;

class UserController extends RestAuthenticatedController
{
    /**
     * Send sms token.
     *
     * @Rest\Post("/users/sms-token")
     */
    public function smsTokenAction(Request $request) {
        $mobile_number = $request->request->get('mobile_number');

        $token = $this->generateToken(4);

        $redis = $this->get('snc_redis.default');
        $redis->set($mobile_number, $token);


        $redis->expire($mobile_number, ApiUtils::MOBILE_TOKEN_LIVE_SECONDS);

        $smsService = $this->get('app.sms_service');
        if (!$smsService->sendSms($token)) {
            return $this->view(ApiUtils::formatError('发送短信失败'), Status::HTTP_NOT_FOUND);
        }

        $data = [
            'mobile_number' => $mobile_number,
            'mobile_token' => $token,
            'expires_at' => time() + ApiUtils::MOBILE_TOKEN_LIVE_SECONDS,
        ];
        return $this->view(ApiUtils::formatSuccess($data), Status::HTTP_CREATED);
    }

    private function generateToken($length = 6) {
        $randStr = str_shuffle('1234567890');
        $rand = substr($randStr, 0, $length);
        return $rand;
    }

    /**
     * Sign up via sms.
     *
     * @Rest\Post("/users/sms")
     */
    public function smsSignupAction() {
    }

    /**
     * Sign up via email.
     *
     * @Rest\Post("/users/email")
     */
    public function emailSignupAction() {
    }

    /**
     * Login
     *
     * @Rest\Post("/users/login")
     *
     * @Login
     */
    public function loginAction(Request $request) {
        $form = $this->createForm(new LoginType());
        $form->bind($request);

        if (!$form->isValid()) {
            return $this->view(ApiUtils::formatError($form->getErrors()), Status::HTTP_NOT_FOUND);
        }

        $user = ApiUtils::objectToArray(new User());
        $user['token'] = 'dsfasf2sf342fda';
        $data = [
            'user' => $user,
        ];
        return $this->view(ApiUtils::formatSuccess($data), Status::HTTP_OK);
    }

    /**
     * Logout
     *
     * @Rest\Post("/users/logout")
     */
    public function logoutAction() {
    }
}