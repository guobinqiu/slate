<?php

namespace Wenwen\FrontendBundle\Controller\API\V1;

use FOS\RestBundle\Controller\Annotations as Rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedFOSRestController;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\EventListener\API\LoginTokenListener;
use Wenwen\FrontendBundle\Form\API\V1\LoginType;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

class UserController extends TokenAuthenticatedFOSRestController
{
    const MOBILE_TOKEN_TTL = 600; //10min

    /**
     * Send sms token.
     *
     * @Rest\Post("/users/sms-token")
     */
    public function smsTokenAction(Request $request) 
    {
        $mobileNumber = $request->request->get('mobile_number');
        $mobileToken = $this->createMobileToken(4);

        $redis = $this->get('snc_redis.default');
        $redis->set($mobileNumber, $mobileToken);
        $redis->expire($mobileNumber, self::MOBILE_TOKEN_TTL);

        $smsService = $this->get('api.sms_service');
        if (!$smsService->sendSms($mobileToken)) {
            return $this->view(ApiUtil::formatError('Failed to send sms'), HttpStatus::HTTP_NOT_FOUND);
        }

        $data = [
            'mobile_number' => $mobileNumber,
            'mobile_token' => $mobileToken,
            'expires_at' => time() + self::MOBILE_TOKEN_TTL,
        ];
        return $this->view(ApiUtil::formatSuccess($data), HttpStatus::HTTP_CREATED);
    }

    /**
     * Sign up via sms.
     *
     * @Rest\Post("/users/sms")
     */
    public function smsSignupAction() 
    {
    }

    /**
     * Sign up via email.
     *
     * @Rest\Post("/users/email")
     */
    public function emailSignupAction() 
    {
    }

    /**
     * Login
     *
     * @Rest\Post("/users/login")
     */
    public function loginAction(Request $request) 
    {
        $form = $this->createForm(new LoginType());
        $form->bind($request);

        if (!$form->isValid()) {
            return $this->view(ApiUtil::formatError($form->getErrors()), HttpStatus::HTTP_NOT_FOUND);
        }

        $user = new User();
        $loginToken = $this->createLoginToken($user);
        $data = [
            'user' => $user,
            'auth_token' => $loginToken,
        ];
        return $this->view(ApiUtil::formatSuccess($data), HttpStatus::HTTP_OK);
    }

    /**
     * Logout
     *
     * @Rest\Post("/users/logout")
     */
    public function logoutAction() 
    {
    }

    private function createMobileToken($length = 6)
    {
        $randStr = str_shuffle('1234567890');
        $rand = substr($randStr, 0, $length);
        return $rand;
    }

    private function createLoginToken(User $user)
    {
        $loginToken = md5(uniqid(rand(), true));
        $redis = $this->get('snc_redis.default');
        $redis->set($loginToken, $user->getId());
        $redis->expire($loginToken, 1800);
        return $loginToken;
    }
}