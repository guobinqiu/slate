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
 * @Route("/questionnaire",requirements={"_scheme"="http"})
 */
class QuestionnaireController extends Controller {

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction()
    {
        return $this->render('WenwenFrontendBundle:Questionnaire:index.html.twig');
    }

    /**
     * @Route("/complete")
     * @Template
     */
    public function completeAction()
    {
        return $this->render('WenwenFrontendBundle:Questionnaire:complete.html.twig');
    }

    /**
     * @Route("/noComplete")
     * @Template
     */
    public function noCompleteAction()
    {
        return $this->render('WenwenFrontendBundle:Questionnaire:noComplete.html.twig');
    }
}
