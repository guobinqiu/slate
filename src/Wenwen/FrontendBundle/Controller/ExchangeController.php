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
 * @Route("/exchange",requirements={"_scheme"="http"})
 */
class ExchangeController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:Exchange:index.html.twig');
    }

    /**
     * @Route("/mobileInfo")
     * @Template
     */
    public function mobileInfoAction()
    {
        return $this->render('WenwenFrontendBundle:Exchange:mobileInfo.html.twig');
    }

    /**
     * @Route("/alipayInfo")
     * @Template
     */
    public function alipayInfoAction()
    {
        return $this->render('WenwenFrontendBundle:Exchange:alipayInfo.html.twig');
    }

    /**
     * @Route("/alipayConfirm")
     * @Template
     */
    public function alipayConfirmAction()
    {
        return $this->render('WenwenFrontendBundle:Exchange:alipayConfirm.html.twig');
    }

    /**
     * @Route("/flowInfo")
     * @Template
     */
    public function flowInfoAction()
    {
        return $this->render('WenwenFrontendBundle:Exchange:flowInfo.html.twig');
    }

    /**
     * @Route("/flowApply")
     * @Template
     */
    public function flowApplyAction()
    {
        return $this->render('WenwenFrontendBundle:Exchange:flowApply.html.twig');
    }

    /**
     * @Route("/finished")
     * @Template
     */
    public function finishedAction()
    {
        return $this->render('WenwenFrontendBundle:Exchange:finished.html.twig');
    }
}
