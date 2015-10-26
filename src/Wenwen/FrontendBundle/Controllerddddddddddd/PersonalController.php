<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/personal",requirements={"_scheme"="http"})
 */
class PersonalController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function profileAction()
    {
        return $this->render('WenwenFrontendBundle:Personal:profile.html.twig');
    }

    /**
     * @Route("/account")
     * @Template
     */
    public function accountAction()
    {
        return $this->render('WenwenFrontendBundle:Personal:account.html.twig');
    }

    /**
     * @Route("/message")
     * @Template
     */
    public function messageAction()
    {
        return $this->render('WenwenFrontendBundle:Personal:message.html.twig');
    }

    /**
     * @Route("/exchangeHistory")
     * @Template
     */
    public function exchangeAction()
    {
        return $this->render('WenwenFrontendBundle:Personal:exchangeHistory.html.twig');
    }

    /**
     * @Route("/taskHistory")
     * @Template
     */
    public function taskAction()
    {
        return $this->render('WenwenFrontendBundle:Personal:taskHistory.html.twig');
    }
}
