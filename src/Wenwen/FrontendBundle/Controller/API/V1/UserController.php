<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\AuthenticatedFOSRestController;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Form\API\V1\LoginType;
use Wenwen\FrontendBundle\Model\API\ApiUtils;
use Wenwen\FrontendBundle\Model\API\Status;

class UserController extends AuthenticatedFOSRestController
{
    /**
     * Send sms token.
     *
     * @Rest\Post("/users/sms-token")
     */
    public function smsTokenAction(Request $request) {
        $mobileNumber = $request->request->get('mobile_number');
        $mobileToken = $this->generateMobileToken(4);

        $redis = $this->get('snc_redis.default');
        $redis->set($mobileNumber, $mobileToken);
        $redis->expire($mobileNumber, ApiUtils::MOBILE_TOKEN_LIVE_SECONDS);

        $smsService = $this->get('api.sms_service');
        if (!$smsService->sendSms($mobileToken)) {
            return $this->view(ApiUtils::formatError('发送短信失败'), Status::HTTP_NOT_FOUND);
        }

        $data = [
            'mobile_number' => $mobileNumber,
            'mobile_token' => $mobileToken,
            'expires_at' => time() + ApiUtils::MOBILE_TOKEN_LIVE_SECONDS,
        ];
        return $this->view(ApiUtils::formatSuccess($data), Status::HTTP_CREATED);
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
     */
    public function loginAction(Request $request) {
        $form = $this->createForm(new LoginType());
        $form->bind($request);

        if (!$form->isValid()) {
            return $this->view(ApiUtils::formatError($form->getErrors()), Status::HTTP_NOT_FOUND);
        }

        $user = new User();
        $loginToken = $this->generateLoginToken($user);

        $redis = $this->get('snc_redis.default');
        $redis->set($loginToken, $user->getId());
        $redis->expire($loginToken, ApiUtils::LOGIN_TOKEN_LIVE_SECONDS);

        $arr = ApiUtils::objectToArray($user);
        $arr['login_token'] = $loginToken;

        $data = [
            'user' => $arr,
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

    private function generateMobileToken($length = 6) {
        $randStr = str_shuffle('1234567890');
        $rand = substr($randStr, 0, $length);
        return $rand;
    }

    private function generateLoginToken(User $user) {
//        return hash('sha256', $user->getId() . $user->getPwd() . time());
        return 'amockedrandomlogintoken';
    }
}