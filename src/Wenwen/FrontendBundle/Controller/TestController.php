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
 * @Route("/test",requirements={"_scheme"="http"})
 */
class TestController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:Test:index.html.twig');
    }

    /**
     * @Route("/indexSimple")
     * @Template
     */
    public function indexSimpleAction()
    {
        return $this->render('WenwenFrontendBundle:Test:indexSimple.html.twig');
    }

    /**
     * @Route("/indexSimple2")
     * @Template
     */
    public function indexSurveyAction()
    {
        return $this->render('WenwenFrontendBundle:Test:indexSimple2.html.twig');
    }
}
