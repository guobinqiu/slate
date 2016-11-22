<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    public function indexAction(Request $request)
    {
        if (!$request->getSession()->has('uid')) {
            $this->setRegisterRouteInSession($request);
            return $this->render('WenwenFrontendBundle:Home:index.html.twig');
        }
        return $this->render('WenwenFrontendBundle:Home:home.html.twig');
    }

}
