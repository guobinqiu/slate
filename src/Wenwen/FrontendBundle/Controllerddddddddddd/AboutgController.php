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
 * @Route("/aboutg",requirements={"_scheme"="http"})
 */
class AboutgController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:About:company.html.twig');
    }
    
    /**
     * @Route("/map")
     * @Template
     */
    public function mapAction()
    {
        return $this->render('WenwenFrontendBundle:About:map.html.twig');
    }

    /**
     * @Route("/links")
     * @Template
     */
    public function linksAction()
    {
        return $this->render('WenwenFrontendBundle:About:links.html.twig');
    }

    /**
     * @Route("/regulations")
     * @Template
     */
    public function regulationsAction()
    {
        return $this->render('WenwenFrontendBundle:About:regulations.html.twig');
    }

    /**
     * @Route("/ww")
     * @Template
     */
    public function wwAction()
    {
        return $this->render('WenwenFrontendBundle:About:91ww.html.twig');
    }

}
