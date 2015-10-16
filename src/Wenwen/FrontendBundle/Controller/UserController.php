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
 * @Route("/user",requirements={"_scheme"="https"})
 */
class UserController extends Controller {

    /**
     * @Route("/register")
     * @Template
     */
    public function registerAction()
    {
        return $this->render('WenwenFrontendBundle:User:register.html.twig');
    }

    /**
     * @Route("/register/active")
     * @Template
     */
    public function registeractiveAction()
    {
        return $this->render('WenwenFrontendBundle:User:active.html.twig');
    }

    /**
     * @Route("/register/finished")
     * @Template
     */
    public function registerfinishedAction()
    {
        return $this->render('WenwenFrontendBundle:User:finished.html.twig');
    }

    /**
     * @Route("/register/weibo")
     * @Template
     */
    public function weiboAction()
    {
        return $this->render('WenwenFrontendBundle:User:weibo.html.twig');
    }

    /**
     * @Route("/register/qq")
     * @Template
     */
    public function qqAction()
    {
        return $this->render('WenwenFrontendBundle:User:qq.html.twig');
    }

    /**
     * @Route("/resetPwd/reset")
     * @Template
     */
    public function resetpwdAction()
    {
        return $this->render('WenwenFrontendBundle:User:resetPwd.html.twig');
    }

    /**
     * @Route("/resetPwd")
     * @Template
     */
    public function resetpwdemailAction()
    {
        return $this->render('WenwenFrontendBundle:User:resetPwdEmail.html.twig');
    }

    /**
     * @Route("/resetPwd/success")
     * @Template
     */
    public function resetsuccessAction()
    {
        return $this->render('WenwenFrontendBundle:User:resetSuccess.html.twig');
    }

}
