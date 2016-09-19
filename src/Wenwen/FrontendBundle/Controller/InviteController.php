<?php

namespace Wenwen\FrontendBundle\Controller;

use Jili\ApiBundle\Utility\PasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/user")
 */
class InviteController extends Controller //implements UserAuthenticationController
{
    const ENCODE_TYPE = 'blowfish';
    const SALT = '羞答答的玫瑰静悄悄地开';

    /**
     * @Route("/invite", name="_user_invite", methods={"GET"})
     */
    public function inviteAction(Request $request)
    {
        $session = $request->getSession();
        if (!$session->has('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WenwenFrontendBundle:User')->find($session->get('uid'));
        $inviteUrl = $this->generateUrl('_user_invite_landing', array('inviteId' => PasswordEncoder::encode(self::ENCODE_TYPE, (string)$user->getId(), self::SALT)), true);
        return $this->render('WenwenFrontendBundle:User:invite.html.twig', array('inviteUrl' => $inviteUrl));
    }

    /**
     * @Route("/lp/{inviteId}", name="_user_invite_landing", methods={"GET"})
     */
    public function inviteLandingAction(Request $request, $inviteId)
    {
        if (isset($inviteId)) {
            $request->getSession()->set('inviteId', PasswordEncoder::decode(self::ENCODE_TYPE, $inviteId, self::SALT));
        }
        return $this->redirect($this->generateUrl('_user_reg'), 301);
    }
}