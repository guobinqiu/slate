<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery;

/**
 * @Route("/ssi_project_survey",requirements={"_scheme"="http"})
 */
class SsiProjectSurveyController extends Controller
{
    const POINT = 700;
    /**
     * @Route("/information/{survey_id}", name="_ssi_project_survey_cover")
     * @Template("WenwenFrontendBundle:SsiProjectSurvey:information.html.twig")
     */
    public function informationAction(Request $request, $survey_id)
    {
        if (!$request->getSession()->has('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $em = $this->getDoctrine()->getEntityManager();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($request->getSession()->get('uid'));

        # SSI Respondent is not active
        if(!$ssi_respondent || !$ssi_respondent->isActive()) {
            throw $this->createNotFoundException('Respondent is not active');
        }

        # Project is not available
        $ssi_project = SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId($em->getConnection(), $ssi_respondent->getId(), $survey_id);
        if(!$ssi_project || !$ssi_project->isOpen()) {
            throw $this->createNotFoundException('Project is not available');
        }

        return array(
          'ssi_project' => $ssi_project,
          'ssi_config' => $this->container->getParameter('ssi_project_survey'),
        );
    }
}
