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
 * @Route("/vote",requirements={"_scheme"="http"})
 */
class VoteController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:Vote:index.html.twig');
    }

    /**
     * @Route("/suggest")
     * @Template
     */
    public function suggestAction()
    {
        return $this->render('WenwenFrontendBundle:Vote:suggest.html.twig');
    }

    /**
     * @Route("/result")
     * @Template
     */
    public function resultAction()
    {
        return $this->render('WenwenFrontendBundle:Vote:result.html.twig');
    }

    /**
     * @Route("/detail")
     * @Template
     */
    public function detailAction()
    {
        return $this->render('WenwenFrontendBundle:Vote:detail.html.twig');
    }

}
