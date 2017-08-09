<?php

namespace Wenwen\FrontendBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Entity\UserTrack;
use Wenwen\FrontendBundle\Form\SignupType;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

/**
 * @Route("/user")
 */
class RegistrationController extends BaseController
{
    /**
     * @Route("/reg", name="_user_reg", methods={"GET", "POST"})
     */
    public function regAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->has('uid')) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $userService = $this->get('app.user_service');
        $provinces = $userService->getProvinceList();
        $cities = $userService->getCityList();

        $ipLocationService = $this->get('app.ip_location_service');
        $locationId = $ipLocationService->getLocationId($request->getClientIp());

        $user = new User();
        $userProfile = new UserProfile();
        if($locationId['status']){
            $userProfile->setCity($locationId['cityId']);
            $userProfile->setProvince($locationId['provinceId']);
        }
        $user->setUserProfile($userProfile);
        $userProfile->setUser($user);
        $form = $this->createForm(new SignupType(), $user);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $fingerprint = $form->get('fingerprint')->getData();
                $regCount = $userService->getRegisteredFingerPrintCount($fingerprint);
                if ($regCount > 1) {
                    // Only allow 1 regsitration for same client(defined by fingerprint) in certain time period.
                    // Return a fake result to bot when blocked by fingerprint
                    $loggerBotRegistration = $this->get('monolog.logger.bot_registration');
                    $loggerBotRegistration->warn(__METHOD__ . ' Too fast registration! clientip=' . $request->getClientIp() . ' fingerprint=' .  $fingerprint . ' count=' . $regCount . ' email=' . $user->getEmail() . ' request=' . $request);
                    return $this->redirect($this->generateUrl('_user_regActive', array('email' => $user->getEmail())));
                }

                $clientIp = $request->getClientIp();
                $userAgent = $request->headers->get('USER_AGENT');
                $inviteId = $session->get('inviteId');
                $allowRewardInviter = $this->allowRewardInviter($request, $fingerprint);
                $this->createUser($user, $clientIp, $userAgent, $inviteId, $fingerprint, $allowRewardInviter);

                if ($form->get('subscribe')->getData() != true) {
                    $em = $this->getDoctrine()->getManager();
                    $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->insertOne($user->getId());
                }

                $token = md5(uniqid(rand(), true));
                $authService = $this->get('app.auth_service');
                $authService->sendConfirmationEmail($user->getId(), $user->getEmail(), $token);

                return $this->redirect($this->generateUrl('_user_regActive', array('email' => $user->getEmail())));
            }
        }

        return $this->render('WenwenFrontendBundle:User:register.html.twig', array(
            'userForm' => $form->createView(),
            'provinces' => $provinces,
            'cities' => $cities,
            'userProfile' => $userProfile
        ));
    }

    /**
     * @Route("/regActive", name="_user_regActive")
     */
    public function regActiveAction(Request $request)
    {
        $email = $request->query->get('email');
        return $this->render('WenwenFrontendBundle:User:emailActive.html.twig', array('email' => $email));
    }

    /**
     * @Route("/signup/confirmRegister", name="_signup_confirm_register", methods={"GET"})
     */
    public function confirmRegisterAction(Request $request)
    {
        $confirmationToken = $request->query->get('confirmation_token');
        $authService = $this->get('app.auth_service');
        $rtn = $authService->confirmEmail($confirmationToken);

        if ($rtn['status'] == 'success') {
            if($this->pushBasicProfile($rtn['userId'])) {
                // 推送用户基本属性
                $request->getSession()->set('uid', $rtn['userId']);
                return $this->redirect($this->generateUrl('_user_regSuccess'));
            }
        } else {
            // Todo 过渡用的，上线24小时后可以删除整个else的内容
            if (!isset($confirmationToken)) {
                return $this->redirect($this->generateUrl('_user_regFailure'));
            }
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array(
                'confirmationToken' => $confirmationToken,
            ));
            if ($user == null) {
                return $this->redirect($this->generateUrl('_user_regFailure'));
            }
            if ($user->isConfirmationTokenExpired()) {
                return $this->redirect($this->generateUrl('_user_regFailure'));
            }
            $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED);
            $user->setRegisterCompleteDate(new \DateTime());
            $user->setLastGetPointsAt(new \DateTime());
            $em->flush();

            $this->pushBasicProfile($user->getId());// 推送用户基本属性
            $request->getSession()->set('uid', $user->getId());
            return $this->redirect($this->generateUrl('_user_regSuccess'));

        }

        return $this->redirect($this->generateUrl('_user_regFailure'));
    }

    /**
     * @Route("/regSuccess", name="_user_regSuccess")
     */
    public function regSuccessAction()
    {
        return $this->render('WenwenFrontendBundle:User:regSuccess.html.twig');
    }

    /**
     * @Route("/regFailure", name="_user_regFailure")
     */
    public function regFailureAction()
    {
        return $this->render('WenwenFrontendBundle:User:regFailure.html.twig');
    }

    /**
     * @Route("/profile_survey", name="_user_profile_survey", methods={"GET"})
     */
    public function profileSurvey(Request $request)
    {
        $userId = $request->getSession()->get('uid');
        if ($userId == null) {
            $this->redirect($this->generateUrl('_homepage'));
        }
        $sop_profiling_info = $this->getSopProfilingSurveyInfo($userId);
        return $this->redirect($sop_profiling_info['profiling']['url']);
    }

    private function pushBasicProfile($userId)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('WenwenFrontendBundle:User')->findOneById($userId);
        if ($user == null) {
            return false;
        }

        $args = array(
            '--user_id=' . $userId,
        );
        $job = new Job('sop:push_basic_profile', $args, true, '91wenwen_sop');
        $job->setMaxRetries(3);

        $em->persist($job);
        $em->flush();

        return true;
    }

    private function getSopProfilingSurveyInfo($user_id) {
        $this->container->get('logger')->debug(__METHOD__ . ' - START - ');
        $surveyService = $this->get('app.survey_service');
        $env = $this->container->get('kernel')->getEnvironment();
        if (in_array($env, array('dev','test'))) {
            // for dummy mode (won't access sop's server at dev or test mode)
            // test环境时不去访问SOP服务器，在circleCI上运行测试case时，访问SOP服务器会超时，导致测试运行极慢
            $surveyService->setDummy(true);
        }
        $sop_profiling_info = $surveyService->getSopProfilingSurveyInfo($user_id);
        $this->container->get('logger')->debug(__METHOD__ . ' - END - ');
        return $sop_profiling_info;
    }

    private function createUser(User $user, $clientIp, $userAgent, $inviteId, $fingerprint, $allowRewardInviter)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();

        try {
            $user->setCreatedRemoteAddr($clientIp);
            $user->setCreatedUserAgent($userAgent);
            if ($allowRewardInviter) {
                $user->setInviteId($inviteId);
            }

            $userTrack = new UserTrack();
            $userTrack->setLastFingerprint(null);
            $userTrack->setCurrentFingerprint($fingerprint);
            $userTrack->setSignInCount(1);
            $userTrack->setLastSignInAt(null);
            $userTrack->setCurrentSignInAt(new \DateTime());
            $userTrack->setLastSignInIp(null);
            $userTrack->setCurrentSignInIp($clientIp);
            $userTrack->setOauth(null);
            $userTrack->setRegisterRoute($this->getRegisterRouteFromSession());
            $this->get('logger')->debug(__METHOD__ . ' ' . $this->getRegisterRouteFromSession());

            $userTrack->setUser($user);
            $user->setUserTrack($userTrack);

            $em->persist($user);
            $em->flush();

        } catch (\PDOException $e) {
            $em->getConnection()->rollBack();
            $em->close();
            if ($e->getCode() === '23000') {
                return $this->createUser($user, $clientIp, $userAgent, $inviteId, $fingerprint, $allowRewardInviter);
            } else {
                throw $e;
            }
        }
    }

    private function allowRewardInviter(Request $request, $fingerprint)
    {
        if (!$request->cookies->has('uid')) {
            //如果用户把cookie删了，就通过fingerprint来判断，fingerprint相同的邀请不给分
            $em = $this->getDoctrine()->getManager();
            $userTrack = $em->getRepository('WenwenFrontendBundle:UserTrack')->findOneBy(array('currentFingerprint' => $fingerprint));
            if ($userTrack == null) {
                return true;
            }
        }
        return false;
    }
}