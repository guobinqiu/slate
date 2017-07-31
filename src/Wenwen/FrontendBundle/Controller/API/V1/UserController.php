<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedFOSRestController;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Form\API\V1\LoginType;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\Status;

class UserController extends TokenAuthenticatedFOSRestController
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
        $redis->expire($mobileNumber, ApiUtil::MOBILE_TOKEN_LIVE_SECONDS);

        $smsService = $this->get('api.sms_service');
        if (!$smsService->sendSms($mobileToken)) {
            return $this->view(ApiUtil::formatError('Failed to send sms'), Status::HTTP_NOT_FOUND);
        }

        $data = [
            'mobile_number' => $mobileNumber,
            'mobile_token' => $mobileToken,
            'expires_at' => time() + ApiUtil::MOBILE_TOKEN_LIVE_SECONDS,
        ];
        return $this->view(ApiUtil::formatSuccess($data), Status::HTTP_CREATED);
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
            return $this->view(ApiUtil::formatError($form->getErrors()), Status::HTTP_NOT_FOUND);
        }

        $user = new User();
        $userAccessToken = $this->generateUserAccessToken($user);

        $redis = $this->get('snc_redis.default');
        $redis->set($userAccessToken, $user->getId());
        $redis->expire($userAccessToken, ApiUtil::USER_ACCESS_TOKEN_LIVE_SECONDS);

        $data = [
            'user' => $user,
            'user_access_token' => $userAccessToken,
        ];
        return $this->view(ApiUtil::formatSuccess($data), Status::HTTP_OK);
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

    private function generateUserAccessToken(User $user) {
//        return hash('sha256', $user->getId() . $user->getPwd() . time());
        return md5(uniqid(rand(), true));
    }
}