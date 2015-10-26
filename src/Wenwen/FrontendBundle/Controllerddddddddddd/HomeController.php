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
 * @Route("/home",requirements={"_scheme"="http"})
 */
class HomeController extends Controller {

    /**
     * @Route("/out")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:Home:index.html.twig');
    }

    /**
     * @Route("/in")
     * @Template
     */
    public function homeAction()
    {
        return $this->render('WenwenFrontendBundle:Home:home.html.twig');
    }
}
