<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wenwen\FrontendBundle\Entity\SurveyGmoNonBusiness;
use Wenwen\FrontendBundle\Form\SurveyGmoNonBusinessType;

/**
 * SurveyGmoNonBusiness controller.
 *
 * @Route("/admin/gmo/report")
 */
class AdminSurveyGmoReportController extends Controller
{
    /**
     * Lists all SurveyGmo entities.
     *
     * @Route("/surveylist", name="admin_gmo_report_surveylist")
     * @Method("GET")
     */
    public function surveylistAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('WenwenFrontendBundle:SurveyGmo')->findBy(array(), array('createdAt' => 'DESC'));

        return $this->render("WenwenFrontendBundle:admin:SurveyGmoReport/surveylist.html.twig", array(
            'entities' => $entities,
        ));
    }

}
