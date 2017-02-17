<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Entity\User;

class HomeController extends BaseController
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cookies = $request->cookies;
        if ($cookies->has(User::REMEMBER_ME_TOKEN)) {
            $user = $em->getRepository('WenwenFrontendBundle:User')->findOneBy(array('rememberMeToken' => $cookies->get(User::REMEMBER_ME_TOKEN)));
            if ($user != null && !$user->isRememberMeTokenExpired()) {
                $request->getSession()->set('uid', $user->getId());
            }
        }

        if (!$request->getSession()->has('uid')) {
            $this->setRegisterRouteInSession($request);
            return $this->render('WenwenFrontendBundle:Home:index.html.twig');
        }

        return $this->render('WenwenFrontendBundle:Home:home.html.twig');
    }
}
