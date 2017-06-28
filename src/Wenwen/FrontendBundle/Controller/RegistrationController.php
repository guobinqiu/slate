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
                if($userService->isRegisteredFingerPrint($fingerprint)){
                    // Only allow 1 regsitration for same client(defined by fingerprint) in certain time period.
                    // Return a fake result to bot when blocked by fingerprint
                    $loggerBotRegistration = $this->get('monolog.logger.bot_registration');
                    $loggerBotRegistration->warn(__METHOD__ . ' Too fast registration! clientip=' . $request->getClientIp() . ' fingerprint=' .  $fingerprint . ' email=' . $user->getEmail() . ' request=' . $request);
                    return $this->redirect($this->generateUrl('_user_regActive', array('email' => $user->getEmail())));
                }

                $em = $this->getDoctrine()->getManager();

                $user->setConfirmationToken(md5(uniqid(rand(), true)));
                $user->setConfirmationTokenExpiredAt(new \DateTime('+ 24 hour'));
                $user->setCreatedRemoteAddr($request->getClientIp());
                $user->setCreatedUserAgent($request->headers->get('USER_AGENT'));

                if (!$request->cookies->has('uid')) {
                    //再加一道防护
                    //如果用户把cookie删了，就通过fingerprint来判断，fingerprint相同的邀请不给分
                    //说实话没啥用
                    //服务端写浏览器是安全的，浏览器传给服务端，懂点技术的人都可以修改这个fingerprint
                    //先试试看吧
                    $userTrack = $em->getRepository('WenwenFrontendBundle:UserTrack')->findOneBy(array('currentFingerprint' => $fingerprint));
                    if ($userTrack == null) {
                        $user->setInviteId($session->get('inviteId'));
                    }
                }

                $userTrack = new UserTrack();
                $userTrack->setLastFingerprint(null);
                $userTrack->setCurrentFingerprint($fingerprint);
                $userTrack->setSignInCount(1);
                $userTrack->setLastSignInAt(null);
                $userTrack->setCurrentSignInAt(new \DateTime());
                $userTrack->setLastSignInIp(null);
                $userTrack->setCurrentSignInIp($request->getClientIp());
                $userTrack->setOauth(null);
                $userTrack->setRegisterRoute($this->getRegisterRouteFromSession());
                $this->get('logger')->debug(__METHOD__ . ' ' .  $this->getRegisterRouteFromSession());

                $userTrack->setUser($user);
                $user->setUserTrack($userTrack);

                $em->persist($user);
                $em->flush();

                if ($form->get('subscribe')->getData() != true) {
                    $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->insertOne($user->getId());
                }

                $this->send_confirmation_email($user);

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
        $confirmation_token = $request->query->get('confirmation_token');

        if (!isset($confirmation_token)) {
            throw new \Exception('无效链接');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array(
            'confirmationToken' => $confirmation_token,
        ));

        if ($user == null) {
            throw new \Exception('无效链接');
        }

        if ($user->isConfirmationTokenExpired()) {
            throw new \Exception('验证码已过期');
        }

        $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED);
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setLastGetPointsAt(new \DateTime());
        $em->flush();

        // 暂时以comment的方式留着，将来可能考虑要给完成注册时的积分
        /*
        $pointService = $this->get('app.point_service');

        // 给当前用户加积分
        $pointService->addPoints(
            $user,
            User::POINT_SIGNUP,
            CategoryType::SIGNUP,
            TaskType::RENTENTION,
            '完成注册',
            null,
            new \DateTime(),
            true,
            PrizeItem::TYPE_SMALL
        );
        */

        $this->pushBasicProfile($user);// 推送用户基本属性

        $request->getSession()->set('uid', $user->getId());

        return $this->redirect($this->generateUrl('_user_regSuccess'));
    }

    /**
     * @Route("/regSuccess", name="_user_regSuccess")
     */
    public function regSuccessAction()
    {
        return $this->render('WenwenFrontendBundle:User:regSuccess.html.twig');
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

    private function send_confirmation_email(User $user)
    {
        $args = array(
            '--subject=[91问问调查网] 请点击链接完成注册，开始有奖问卷调查',
            '--email='.$user->getEmail(),
            '--name='.$user->getNick(),
            '--confirmation_token='.$user->getConfirmationToken(),
        );
        $job = new Job('mail:signup_confirmation', $args, true, '91wenwen_signup', Job::PRIORITY_HIGH);
        $job->setMaxRetries(3);
        $em = $this->getDoctrine()->getManager();
        $em->persist($job);
        $em->flush();
    }

    private function pushBasicProfile(User $user)
    {
        $args = array(
            '--user_id=' . $user->getId(),
        );
        $job = new Job('sop:push_basic_profile', $args, true, '91wenwen_sop');
        $job->setMaxRetries(3);
        $em = $this->getDoctrine()->getManager();
        $em->persist($job);
        $em->flush();
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
}