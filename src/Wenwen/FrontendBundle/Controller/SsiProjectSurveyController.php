<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Jili\ApiBundle\Utility\SopUtil;
use SOPx\Auth\V1_1\Util;



/**
 * @Route("/ssi_project_survey",requirements={"_scheme"="http"})
 */
class SsiProjectSurveyController extends Controller
{

    /**
     * @Route("/information/{survey_id}")
     * @Template("WenwenFrontendBundle:SsiProjectSurvey:information.html.twig")
     */
    public function informationAction(Request $request, $survey_id)
    {
        return array();
    }
}
