<?php

namespace Wenwen\FrontendBundle\Services;

use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\AuthEmail;

/**
 * Authentication Related Service
 */
class AuthService
{
    private $logger;

    private $em;

    public function __construct(LoggerInterface $logger,
                                EntityManager $em)
    {
        $this->logger = $logger;
        $this->em = $em;
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
        $rtn['status'] = 'success';
        $rtn['errMsg'] = '';

        if(is_null($userId) || is_null($email) || is_null($token) ){
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = 'Invalid params.';
            $this->logger->warn(__METHOD__ . ' Invalid params userId=' . $userId . ' email=' . $email . ' token=' . $token);
            return $rtn;
        }

        $this->logger->debug(__METHOD__ . ' START userId=' . $userId . ' email=' . $email);

        try {
            $user = $this->em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
            if(is_null($user)){
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = 'Invalid user.';
                $this->logger->warn(__METHOD__ . ' userId=' . $userId . ' email=' . $email . ' errMsg=' . $rtn['errMsg']);
                return $rtn;
            }

            $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'email' => $email,
            ));

            if($authEmail){
                $this->logger->debug(__METHOD__ . ' Already exist userId=' . $userId . ' email=' . $email);
                $diffInSeconds = (new \DateTime())->getTimestamp() - $authEmail->getUpdatedAt()->getTimestamp();

                if ( $diffInSeconds < 60 ){
                    $rtn['status'] = 'failure';
                    $rtn['errMsg'] = 'Too many request for email confirmation';
                    $this->logger->warn(__METHOD__ . ' userId=' . $userId . ' email=' . $email . ' errMsg=' . $rtn['errMsg']);
                    return $rtn;
                }

                $this->logger->debug(__METHOD__ . ' Re-send confirm email for exist userId=' . $userId . ' email=' . $email);

                $authEmail->setToken($token);
                $authEmail->setExpiredAt(new \DateTime(AuthEmail::EXPIRE_DURATION));
                $authEmail->setUpdatedAt(new \DateTime());

            } else {
                $authEmail = new AuthEmail();
                $authEmail->setUser($user);
                $authEmail->setEmail($email);
                $authEmail->setToken($token);

                $this->em->persist($authEmail);
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
            $rtn['status'] = 'error';
            $rtn['errMsg'] = $e->getMessage();
            $this->logger->error(__METHOD__ . ' ' . $rtn['errMsg']);
            return $rtn;
        }
        $this->logger->info(__METHOD__ . ' Queued an signup_confirmation email for userId=' . $userId . ' email=' . $email . ' token=' . $token);
        return $rtn;
    }

    /**
     * token validation
     * @param $token
     * @return array
     */
    public function confirmEmail($token){

        if(is_null($token) ){
            $rtn['status'] = 'failure';
            $rtn['errMsg'] = 'Invalid params.';
            $this->logger->warn(__METHOD__ . ' token=' . $token . $rtn['errMsg']);
            return $rtn;
        }

        $this->logger->debug(__METHOD__ . ' START token=' . $token);

        $rtn = array();
        $rtn['status'] = 'success';
        $rtn['errMsg'] = '';
        $rtn['user'] = '';
        try{
            $authEmail = $this->em->getRepository('WenwenFrontendBundle:AuthEmail')->findOneBy(array(
                'token' => $token,
            ));

            if ($authEmail == null) {
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = 'token not exist.';
                $this->logger->warn(__METHOD__ . ' token=' . $token . $rtn['errMsg']);
                return $rtn;
            }

            if ($authEmail->isTokenExpired()) {
                $rtn['status'] = 'failure';
                $rtn['errMsg'] = 'token expired.';
                $this->logger->warn(__METHOD__ . ' token=' . $token . $rtn['errMsg']);
                return $rtn;
            }

            $userId = $authEmail->getUser()->getId();

            // -------------
            // Should refactor here -> split below to other service

            $user = $authEmail->getUser();

            $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED);
            $user->setRegisterCompleteDate(new \DateTime());

            $this->em->remove($authEmail);
            $this->em->flush();

            // -------------

            $rtn['userId'] = $userId;

        } catch (\Exception $e){
            $rtn['status'] = 'error';
            $rtn['errMsg'] = $e->getMessage();
            $this->logger->error(__METHOD__ . ' ' . $rtn['errMsg']);
            return $rtn;
        }
        $this->logger->info(__METHOD__ . ' Confirmed an email token for userId=' . $userId . ' email=' . $authEmail->getEmail());
        return $rtn;
    }


}
