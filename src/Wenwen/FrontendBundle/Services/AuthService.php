<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\AuthEmail;
use Wenwen\FrontendBundle\Entity\AuthRememberMe;
use Wenwen\FrontendBundle\Entity\AuthPasswordReset;

/**
 * Authentication Related Service
 */
class AuthService
{
    const KEY_STATUS  = 'status';
    const KEY_MESSAGE = 'message';
    const KEY_USERID  = 'userId';
    const KEY_EMAIL   = 'email';
    const KEY_TOKEN   = 'token';
    const KEY_EXPIREDAT = 'expiredAt';

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILURE = 'failure';
    const STATUS_ERROR   = 'error';

    const MSG_INVALID_PARAMS = 'Invalid params.';
    const MSG_INVALID_USER = 'Invalid user.';
    const MSG_INVALID_EMAIL = 'Invalid email.';
    const MSG_TOKEN_CREATED = 'Token created.';
    const MSG_TOKEN_UPDATED = 'Token updated.';
    const MSG_TOKEN_NOTFOUND = 'Token not found.';
    const MSG_TOKEN_FOUND = 'Token found.';
    const MSG_TOKEN_EXPIRED = 'Token expired.';
    const MSG_MALICIOUS_REQUEST = 'Malicious request for email confirmation';
    const MSG_PASSWORD_RESETED= 'Password reseted.';

    const REMEMBER_ME_TOKEN = 'ww_passport';

    private $logger;

    private $em;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
    }


    public function generateRememberMeToken($userId){
        $rtn = array();
        $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
        $rtn[self::KEY_USERID] = $userId;

        if(is_null($userId) ){
            $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
            $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_PARAMS;
            $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }

        try {
            $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
            if(is_null($user)){
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_USER;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            }

            $token = md5(uniqid(rand(), true));
            $expiredAt = new \DateTime('+ 30 days');
            $authRememberMe = $this->em->getRepository('WenwenFrontendBundle:AuthRememberMe')->findOneByUser($user);
            if(is_null($authRememberMe)){
                $authRememberMe = new AuthRememberMe();
                $authRememberMe->setUser($user);
                $authRememberMe->setToken($token);
                $authRememberMe->setExpiredAt($expiredAt);
                $this->em->persist($authRememberMe);
                $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_CREATED;
                $rtn[self::KEY_TOKEN] = $authRememberMe->getToken();
                $rtn[self::KEY_EXPIREDAT] = $authRememberMe->getExpiredAt();
                $this->logger->debug(__METHOD__ . ' ' . json_encode($rtn));
            } else {
                $authRememberMe->setUser($user);
                $authRememberMe->setToken($token);
                $authRememberMe->setExpiredAt($expiredAt);
                $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_UPDATED;
                $rtn[self::KEY_TOKEN] = $authRememberMe->getToken();
                $rtn[self::KEY_EXPIREDAT] = $authRememberMe->getExpiredAt();
                $this->logger->debug(__METHOD__ . ' ' . json_encode($rtn));
            }
            $this->em->flush();
        } catch (\Exception $e) {
            $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
            $rtn[self::KEY_MESSAGE] = $e->getMessage();
            $this->logger->error(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }
        $this->logger->info(__METHOD__ . ' ' . json_encode($rtn));
        return $rtn;
    }

    public function findRememberMeToken($token){
        $rtn = array();
        $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
        $rtn[self::KEY_TOKEN] = $token;

        if(is_null($token) ){
            $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
            $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_PARAMS;
            $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }

        try {
            $authRememberMe = $this->em->getRepository('WenwenFrontendBundle:AuthRememberMe')->findOneByToken($token);
            if(is_null($authRememberMe)) {
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_NOTFOUND;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            } else {
                $rtn[self::KEY_USERID] = $authRememberMe->getUser()->getId();
                if($authRememberMe->isTokenExpired()){
                    $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                    $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_EXPIRED;
                    $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                    return $rtn;
                }
            }
        } catch (\Exception $e) {
            $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
            $rtn[self::KEY_MESSAGE] = $e->getMessage();
            $this->logger->error(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }
        $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
        $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_FOUND;
        $this->logger->debug(__METHOD__ . ' ' . json_encode($rtn));
        return $rtn;
    }

    /**
     * Send a confirmation email to see whether this email address should belong to a user
     * @param $userId
     * @param $email
     * @param $token
     * @return array
     */
    public function sendConfirmationEmail($userId, $email, $token) {
        $rtn = array();
        $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
        $rtn[self::KEY_USERID] = $userId;
        $rtn[self::KEY_EMAIL] = $email;
        $rtn[self::KEY_TOKEN] = $token;

        if(is_null($userId) || is_null($email) || is_null($token) ){
            $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
            $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_PARAMS;
            $this->logger->debug(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }

        try {
            $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
            if(is_null($user)){
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_USER;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            }

            $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));

            if($authEmail){
                $diffInSeconds = (new \DateTime())->getTimestamp() - $authEmail->getUpdatedAt()->getTimestamp();

                if ( $diffInSeconds < 60 ){
                    $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                    $rtn[self::KEY_MESSAGE] = self::MSG_MALICIOUS_REQUEST;
                    $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                    return $rtn;
                }

                $authEmail->setToken($token);
                $authEmail->setExpiredAt(new \DateTime(AuthEmail::EXPIRE_DURATION));
                $authEmail->setUpdatedAt(new \DateTime());
                $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_UPDATED;
                $this->logger->debug(__METHOD__ . ' ' . json_encode($rtn));
            } else {
                $authEmail = new AuthEmail();
                $authEmail->setUser($user);
                $authEmail->setEmail($email);
                $authEmail->setToken($token);
                $this->em->persist($authEmail);
                $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_CREATED;
            }

            $args = array(
                '--subject=[91问问调查网] 请点击链接完成注册',
                '--email='.$email,
                '--name='.$user->getNick(),
                '--confirmation_token='.$authEmail->getToken(),
            );
            $job = new Job('mail:signup_confirmation', $args, true, '91wenwen_signup', Job::PRIORITY_HIGH);
            $job->setMaxRetries(3);
            $this->em->persist($job);
            $this->em->flush();
        } catch (\Exception $e) {
            $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
            $rtn[self::KEY_MESSAGE] = $e->getMessage();
            $this->logger->error(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }
        $this->logger->info(__METHOD__ . ' ' . json_encode($rtn));
        return $rtn;
    }

    /**
     * token validation
     * @param $token
     * @return array
     */
    public function confirmEmail($token){
        $rtn = array();
        $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
        $rtn[self::KEY_TOKEN] = $token;

        if(is_null($token) ){
            $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
            $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_PARAMS;
            $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }

        try{
            $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'token' => $token,
            ));

            if ($authEmail == null) {
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_NOTFOUND;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            }

            if ($authEmail->isTokenExpired()) {
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_EXPIRED;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            }

            $rtn[self::KEY_USERID] = $authEmail->getUser()->getId();

            // -------------
            // Should refactor here -> split below to other service

            $user = $authEmail->getUser();

            $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED);
            $user->setRegisterCompleteDate(new \DateTime());

            $this->em->remove($authEmail);
            $this->em->flush();

            // -------------

            $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_FOUND;

        } catch (\Exception $e){
            $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
            $rtn[self::KEY_MESSAGE] = $e->getMessage();
            $this->logger->error(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }
        $this->logger->info(__METHOD__ . ' ' . json_encode($rtn));
        return $rtn;
    }

    /**
     * Send a password reset email
     * @param $email
     * @return array
     */
    public function sendPasswordResetEmail($email) {
        $rtn = array();
        $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
        $rtn[self::KEY_EMAIL] = $email;

        if(is_null($email)){
            $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
            $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_PARAMS;
            $this->logger->debug(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }

        try {
            $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneByEmail($email);
            if(is_null($user)){
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_EMAIL;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            }

            $token = md5(uniqid(rand(), true));

            $authPasswordReset = $this->em->getRepository('WenwenFrontendBundle:AuthPasswordReset')->findOneByEmail($email);

            if($authPasswordReset){
                $diffInSeconds = (new \DateTime())->getTimestamp() - $authPasswordReset->getUpdatedAt()->getTimestamp();

                if ( $diffInSeconds < 60 ){
                    $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                    $rtn[self::KEY_MESSAGE] = self::MSG_MALICIOUS_REQUEST;
                    $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                    return $rtn;
                }

                $authPasswordReset->setToken($token);
                $authPasswordReset->setExpiredAt(new \DateTime(AuthPasswordReset::EXPIRE_DURATION));
                $authPasswordReset->setUpdatedAt(new \DateTime());
                $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_UPDATED;
                $this->logger->debug(__METHOD__ . ' ' . json_encode($rtn));
            } else {
                $authPasswordReset = new AuthPasswordReset();
                $authPasswordReset->setUser($user);
                $authPasswordReset->setEmail($email);
                $authPasswordReset->setToken($token);
                $this->em->persist($authPasswordReset);
                $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_CREATED;
            }

            $args = array(
                '--subject=[91问问调查网] 帐号密码重置',
                '--email='.$email,
                '--name='.$user->getNick(),
                '--reset_password_token='.$authPasswordReset->getToken(),
            );
            $job = new Job('mail:reset_password', $args, true, '91wenwen_reset', Job::PRIORITY_HIGH);
            $job->setMaxRetries(3);
            $this->em->persist($job);
            $this->em->flush();
        } catch (\Exception $e) {
            $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
            $rtn[self::KEY_MESSAGE] = $e->getMessage();
            $this->logger->error(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }
        $this->logger->info(__METHOD__ . ' ' . json_encode($rtn));
        return $rtn;
    }

    /**
     * password reset token validation
     * @param $token
     * @return array
     */
    public function confirmPasswordReset($token){
        $rtn = array();
        $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
        $rtn[self::KEY_TOKEN] = $token;

        if(is_null($token) ){
            $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
            $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_PARAMS;
            $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }

        try{
            $authPasswordReset = $this->em->getRepository('WenwenFrontendBundle:AuthPasswordReset')->findOneByToken($token);

            if ($authPasswordReset == null) {
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_NOTFOUND;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            }

            if ($authPasswordReset->isTokenExpired()) {
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_EXPIRED;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            }

            $rtn[self::KEY_USERID] = $authPasswordReset->getUser()->getId();
            $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
            $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_FOUND;

        } catch (\Exception $e){
            $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
            $rtn[self::KEY_MESSAGE] = $e->getMessage();
            $this->logger->error(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }
        $this->logger->info(__METHOD__ . ' ' . json_encode($rtn));
        return $rtn;
    }

    /**
     * password reset
     * @param $token
     * @param $password
     * @return array
     */
    public function resetPassword($token, $password){
        $rtn = array();
        $rtn[self::KEY_STATUS] = self::STATUS_SUCCESS;
        $rtn[self::KEY_TOKEN] = $token;

        if(is_null($token) || is_null($password)){
            $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
            $rtn[self::KEY_MESSAGE] = self::MSG_INVALID_PARAMS;
            $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }

        try{
            $authPasswordReset = $this->em->getRepository('WenwenFrontendBundle:AuthPasswordReset')->findOneByToken($token);

            if ($authPasswordReset == null) {
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_NOTFOUND;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            }

            if ($authPasswordReset->isTokenExpired()) {
                $rtn[self::KEY_STATUS] = self::STATUS_FAILURE;
                $rtn[self::KEY_MESSAGE] = self::MSG_TOKEN_EXPIRED;
                $this->logger->warn(__METHOD__ . ' ' . json_encode($rtn));
                return $rtn;
            }

            $user = $authPasswordReset->getUser();

            $rtn[self::KEY_USERID] = $user->getId();

            $user->setPwd($password);

            $this->em->remove($authPasswordReset);
            $this->em->flush();

            $rtn[self::KEY_MESSAGE] = self::MSG_PASSWORD_RESETED;

        } catch (\Exception $e){
            $rtn[self::KEY_STATUS] = self::STATUS_ERROR;
            $rtn[self::KEY_MESSAGE] = $e->getMessage();
            $this->logger->error(__METHOD__ . ' ' . json_encode($rtn));
            return $rtn;
        }
        $this->logger->info(__METHOD__ . ' ' . json_encode($rtn));
        return $rtn;
    }

}
