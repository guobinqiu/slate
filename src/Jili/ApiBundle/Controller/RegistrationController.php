<?php

namespace Jili\ApiBundle\Controller;

use Doctrine\ORM\EntityManager;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserProfile;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Jili\FrontendBundle\Form\Type\SignupType;
use Symfony\Component\Validator\Constraints as Assert;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

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

        $em = $this->getDoctrine()->getManager();
        $provinces = $em->getRepository('JiliApiBundle:ProvinceList')->findAll();
        $cities = $em->getRepository('JiliApiBundle:CityList')->findAll();

        $user = new User();
        $userProfile = new UserProfile();
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

                $em->persist($user);
                $em->flush();

                if ($form->get('subscribe')->getData() != true) {
                    $em->getRepository('JiliApiBundle:UserEdmUnsubscribe')->insertOne($user->getId());
                }

                $this->send_confirmation_email($user, $em);

                return $this->redirect($this->generateUrl('_user_regActive', array('email' => $user->getEmail())));
            }
        }

        return $this->render('WenwenFrontendBundle:User:register.html.twig', array(
            'userForm' => $form->createView(),
            'provinces' => $provinces,
            'cities' => $cities,
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
        $user = $em->getRepository('JiliApiBundle:User')->findOneBy(array(
            'confirmationToken' => $confirmation_token,
            'isEmailConfirmed' => User::EMAIL_NOT_CONFIRMED,
        ));

        if ($user == null) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array('error' => '无效链接'));
        }

        if ($user->isConfirmationTokenExpired()) {
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array('error' => '验证码已过期'));
        }

        $em->getConnection()->beginTransaction();
        try {
            $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED);
            $user->setRegisterCompleteDate(new \DateTime());
            $user->setPoints(User::POINT_SIGNUP);
            $user->setLastGetPointsAt(new \DateTime());

            $classPointHistory = 'Jili\ApiBundle\Entity\PointHistory0'. ($user->getId() % 10);
            $pointHistory = new $classPointHistory();
            $pointHistory->setUserId($user->getId());
            $pointHistory->setPointChangeNum(User::POINT_SIGNUP);
            $pointHistory->setReason(CategoryType::SIGNUP);
            $em->persist($pointHistory);

            $classTaskHistory = 'Jili\ApiBundle\Entity\TaskHistory0'. ($user->getId() % 10);
            $taskHistory = new $classTaskHistory();
            $taskHistory->setUserid($user->getId());
            $taskHistory->setOrderId(0);
            $taskHistory->setOcdCreatedDate(new \DateTime());
            $taskHistory->setCategoryType(CategoryType::SIGNUP);
            $taskHistory->setTaskType(TaskType::RENTENTION);
            $taskHistory->setTaskName('完成注册');
            $taskHistory->setDate(new \DateTime());
            $taskHistory->setPoint(User::POINT_SIGNUP);
            $taskHistory->setStatus(1);
            $em->persist($taskHistory);

            $em->flush();
            $em->getConnection()->commit();

        } catch(\Exception $e) {
            $em->getConnection()->rollBack();
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array('error' => $e->getMessage()));
        }

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