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
 * @Route("/advertisement",requirements={"_scheme"="http"})
 */
class AdvertisementController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:Advertisement:index.html.twig');
    }

    /**
     * @Route("/bangwoya")
     * @Template
     */
    public function bangwoyaAction()
    {
        return $this->render('WenwenFrontendBundle:Advertisement:bangwoya.html.twig');
    }

    /**
     * @Route("/game")
     * @Template
     */
    public function gameAction()
    {
        return $this->render('WenwenFrontendBundle:Advertisement:game.html.twig');
    }

    /**
     * @Route("/offer99")
     * @Template
     */
    public function offer99Action()
    {
        return $this->render('WenwenFrontendBundle:Advertisement:offer99.html.twig');
    }

    /**
     * @Route("/shopList")
     * @Template
     */
    public function shopListAction()
    {
        return $this->render('WenwenFrontendBundle:Advertisement:shopList.html.twig');
    }

    /**
     * @Route("/shopDetail")
     * @Template
     */
    public function shopDetailAction()
    {
        return $this->render('WenwenFrontendBundle:Advertisement:shopDetail.html.twig');
    }
}
