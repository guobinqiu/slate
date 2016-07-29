<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery;
use Wenwen\FrontendBundle\ServiceDependency\CacheKeys;

/**
 * @Route("/ssi_project_survey",requirements={"_scheme"="http"})
 */
class SsiProjectSurveyController extends Controller
{
    /**
     * @Route("/information/{survey_id}", name="_ssi_project_survey_cover")
     * @Template("WenwenFrontendBundle:SsiProjectSurvey:information.html.twig")
     */
    public function informationAction(Request $request, $survey_id)
    {
        if (!$request->getSession()->has('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

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
        if (!$request->getSession()->has('uid')) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $userId = $request->getSession()->get('uid');
        $cacheSettings = $this->container->getParameter('cache_settings');
        if ($cacheSettings['enable']) {
            $redis = $this->get('snc_redis.default');
            $redis->del(CacheKeys::getOrderHtmlSurveyListKey($userId));
        }

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
