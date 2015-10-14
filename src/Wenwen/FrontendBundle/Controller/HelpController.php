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
 * @Route("/help",requirements={"_scheme"="http"})
 */
class HelpController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:Help:index.html.twig');
    }

    /**
     * @Route("/newGuide")
     * @Template
     */
    public function newGuideAction()
    {
        return $this->render('WenwenFrontendBundle:Help:newGuide.html.twig');
    }

    /**
     * @Route("/search")
     * @Template
     */
    public function searchAction()
    {
        return $this->render('WenwenFrontendBundle:Help:search.html.twig');
    }

    /**
     * @Route("/feedback")
     * @Template
     */
    public function feedbackAction()
    {
        return $this->render('WenwenFrontendBundle:Help:feedback.html.twig');
    }

    /**
     * @Route("/confirm")
     * @Template
     */
    public function confirmAction()
    {
        return $this->render('WenwenFrontendBundle:Help:confirm.html.twig');
    }

    /**
     * @Route("/finished")
     * @Template
     */
    public function finishedAction()
    {
        return $this->render('WenwenFrontendBundle:Help:finished.html.twig');
    }
}
