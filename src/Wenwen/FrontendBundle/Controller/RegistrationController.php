<?php

namespace Wenwen\FrontendBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Entity\PrizeItem;
use Wenwen\FrontendBundle\Model\TaskType;
use Wenwen\FrontendBundle\Entity\User;
use Wenwen\FrontendBundle\Entity\UserProfile;
use Wenwen\FrontendBundle\Entity\UserTrack;
use Wenwen\FrontendBundle\Form\SignupType;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;
use Wenwen\FrontendBundle\Services\AuthService;

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
                $canRewardInviter = $userService->canRewardInviter($this->isUserLoggedIn(), $fingerprint);
                $recruitRoute = $this->getRegisterRouteFromSession();

                $userService = $this->get('app.user_service');
                $user = $userService->createUser($user, $clientIp, $userAgent, $inviteId, $canRewardInviter);
                $userService->createUserTrack($user, $clientIp, $fingerprint, $recruitRoute);

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
        $em = $this->getDoctrine()->getManager();
        $userService = $this->get('app.user_service');

        $ownerType = $this->getOwnerTypeFromSession($request);
        $this->get('logger')->info(__METHOD__ . 'email ownerType=' . $ownerType);

        if ($rtn['status'] == AuthService::STATUS_SUCCESS) {
            $user = $em->getRepository('WenwenFrontendBundle:User')->find($rtn['userId']);
            if ($user == null) {
                return $this->redirect($this->generateUrl('_user_regFailure'));
            }
            $this->get('app.user_service')->createSopRespondent($user->getId(), $ownerType);
            $userService->pushBasicProfileJob($user->getId());
            $request->getSession()->set('uid', $rtn['userId']);
            return $this->redirect($this->generateUrl('_user_regSuccess'));
        } else {
            // Todo 过渡用的，上线24小时后可以删除整个else的内容
            if (!isset($confirmationToken)) {
                return $this->redirect($this->generateUrl('_user_regFailure'));
            }
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

            $this->get('app.user_service')->createSopRespondent($user->getId(), $ownerType);
            $userService->pushBasicProfileJob($user->getId());
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
}