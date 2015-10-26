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
 * @Route("/exception",requirements={"_scheme"="http"})
 */
class ExceptionController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:Exception:index.html.twig');
    }
}
