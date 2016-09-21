<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery;

/**
 * @Route("/ssi_project_survey")
 */
class SsiProjectSurveyController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/information/{survey_id}", name="_ssi_project_survey_cover")
     * @Template("WenwenFrontendBundle:SsiProjectSurvey:information.html.twig")
     */
    public function informationAction(Request $request, $survey_id)
    {
        $em = $this->getDoctrine()->getManager();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($request->getSession()->get('uid'));

        # SSI Respondent is not active
        if (!$ssi_respondent || !$ssi_respondent->isActive()) {
            throw $this->createNotFoundException('Respondent is not active');
        }

        # Project is not available
        $ssi_survey = SsiProjectRespondentQuery::retrieveSurveyBySsiRespondentIdAndSsiProjectId($em->getConnection(), $ssi_respondent->getId(), $survey_id);
        if (!$ssi_survey || !$ssi_survey->isOpen()) {
            throw $this->createNotFoundException('Project is not available');
        }

        return array(
          'ssi_survey' => $ssi_survey,
          'ssi_config' => $this->container->getParameter('ssi_project_survey'),
        );
    }

    /**
     * @Route("/complete", name="_ssi_project_survey_complete")
     * @Template("WenwenFrontendBundle:SsiProjectSurvey:complete.html.twig")
     */
    public function completeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($request->getSession()->get('uid'));

        # SSI Respondent is not active
        if (!$ssi_respondent || !$ssi_respondent->isActive()) {
            throw $this->createNotFoundException('Respondent is not active');
        }

        \VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery::completeSurveysForRespondent(
            $em->getConnection(),
            $ssi_respondent->getId()
        );



        return array();
    }
}
