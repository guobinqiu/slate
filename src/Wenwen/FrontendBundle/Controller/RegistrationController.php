<?php

namespace Wenwen\FrontendBundle\Controller;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Form\SignupType;

/**
 * @Route("/user")
 */
class RegistrationController extends Controller
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
                $confirmationToken = md5($user->getEmail() . $user->getPwd() . time());
                $user->setConfirmationToken($confirmationToken);
                $user->setConfirmationTokenExpiredAt(new \DateTime('+ 24 hour'));
                $user->setCreatedRemoteAddr($request->getClientIp());
                $user->setCreatedUserAgent($request->headers->get('USER_AGENT'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                if ($form->get('subscribe')->getData() != true) {
                    $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->insertOne($user->getId());
                }

                $this->send_confirmation_email($user, $em);

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
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array('error' => '无效链接'));
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array(
            'confirmationToken' => $confirmation_token,
            'isEmailConfirmed' => User::EMAIL_NOT_CONFIRMED,
        ));

        if ($user == null) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array('error' => '无效链接'));
        }

        if ($user->isConfirmationTokenExpired()) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array('error' => '验证码已过期'));
        }

        $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED);
        $user->setRegisterCompleteDate(new \DateTime());
        $user->setLastGetPointsAt(new \DateTime());
        $em->flush();

        $this->get('app.user_service')->addPoints($user, User::POINT_SIGNUP, CategoryType::SIGNUP, TaskType::RENTENTION, '完成注册');

        $this->pushBasicProfile($user, $em);

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
        $sop_profiling_info = $this->getSopProfilingSurveyInfo($userId);
        return $this->redirect($sop_profiling_info['profiling']['url']);
    }

    private function send_confirmation_email(User $user, EntityManager $em)
    {
        $args = array(
            '--subject=[91问问调查网] 请点击链接完成注册，开始有奖问卷调查',
            '--email='.$user->getEmail(),
            '--name='.$user->getNick(),
            '--confirmation_token='.$user->getConfirmationToken(),
        );
        $job = new Job('mail:signup_confirmation', $args, true, '91wenwen_signup', Job::PRIORITY_HIGH);
        $em->persist($job);
        $em->flush();
    }

    private function pushBasicProfile(User $user, EntityManager $em)
    {
        $args = array(
            '--user_id=' . $user->getId(),
        );
        $job = new Job('sop:push_basic_profile', $args, true, '91wenwen_sop');
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