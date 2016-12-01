<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Acl\Exception\Exception;
use Wenwen\AppBundle\Entity\CintUserAgreementParticipationHistory;
use Wenwen\FrontendBundle\Model\CategoryType;
use Wenwen\FrontendBundle\Model\SurveyStatus;
use Wenwen\FrontendBundle\Model\TaskType;

/**
 * @Route("/cint_project_survey")
 */
class ProjectSurveyCintController extends BaseController implements UserAuthenticationController
{
    const AGREEMENT_POINT = 1;
    const COMMENT = '同意参与海外市场调查项目';

    /**
     * @Route("/agreement_complete", name="_cint_project_survey_agreement_complete")
     */
    public function agreementCompleteAction(Request $request)
    {
        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();

        $history = $em->getRepository('WenwenAppBundle:CintUserAgreementParticipationHistory')->findOneByUserId($user_id);
        if ($history) {
            return $this->render('WenwenFrontendBundle:ProjectSurveyCint:agreementComplete.html.twig');
        }

        $params = $request->query->all();

        $sop_config = $this->container->getParameter('sop');
        $cint_config = $this->container->getParameter('cint');
        $status_map = $cint_config['user_agreement'];

        // Verify signature
        $auth = new \SOPx\Auth\V1_1\Client($sop_config['auth']['app_id'], $sop_config['auth']['app_secret']);
        $sig = $params['sig'];
        unset($params['sig']);

        $result = $auth->verifySignature($sig, $params);

        if (!$result['status']) {
            $this->container->get('logger')->error(__METHOD__ . ' errMsg='.$result['msg']);
            return new Response('authentication failed', 400);
        }

        $user = $em->getRepository('WenwenFrontendBundle:User')->find($user_id);

        // start transaction
        $em->getConnection()->beginTransaction();

        try {
            // insert history
            $history_model = new CintUserAgreementParticipationHistory();
            $history_model->setUserId($user_id);
            $history_model->setAgreementStatus($status_map[mb_strtolower($params['agreement_status'])]);
            $em->persist($history_model);
            $em->flush();

            // add point
            $this->get('app.point_service')->addPoints(
                $user,
                self::AGREEMENT_POINT,
                CategoryType::CINT_EXPENSE,
                TaskType::RENTENTION,
                self::COMMENT,
                $history_model
            );

            $em->getConnection()->commit();
        } catch (\Exception $e) {

            $this->get('logger')->crit("Exception: " . $e->getMessage());

            $em->getConnection()->rollback();
            throw $e;
        }
        return $this->render('WenwenFrontendBundle:ProjectSurveyCint:agreementComplete.html.twig');
    }

    /**
     * @Route("/information", name="_cint_project_survey_information", options={"expose"=true})
     */
    public function informationAction(Request $request)
    {
        $cint_research = $request->query->get('cint_research');
        $user = $this->getCurrentUser();
        $app_mid = $this->get('app.survey_service')->getSopRespondentId($user->getId());
        $this->get('app.cint_survey_service')->createStatusHistory(
            $app_mid,
            $cint_research['survey_id'],
            SurveyStatus::STATUS_INIT,
            SurveyStatus::UNANSWERED,
            $request->getClientIp()
        );
        return $this->render('WenwenFrontendBundle:ProjectSurveyCint:information.html.twig', array('cint_research' => $cint_research));
    }

    /**
     * @Route("/forward", name="_cint_project_survey_forward")
     */
    public function forwardAction(Request $request)
    {
        $cint_research = $request->query->get('cint_research');
        $user = $this->getCurrentUser();
        $app_mid = $this->get('app.survey_service')->getSopRespondentId($user->getId());
        $this->get('app.cint_survey_service')->createStatusHistory(
            $app_mid,
            $cint_research['survey_id'],
            SurveyStatus::STATUS_FORWARD,
            SurveyStatus::UNANSWERED,
            $request->getClientIp()
        );
        $cint_research = $this->get('app.cint_survey_service')->addSurveyUrlToken($cint_research, $user->getId());
        return $this->redirect($cint_research['url']);
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_cint_project_survey_endlink")
     */
    public function endlinkAction(Request $request, $survey_id, $answer_status)
    {
        $tid = $request->query->get('tid');
        $app_mid = $request->query->get('app_mid');
        if (!SurveyStatus::isValid($answer_status)) {
            throw new \InvalidArgumentException("cint invalid answer status: {$answer_status}");
        }
        $user = $this->getCurrentUser();
        $app_mid2 = $this->get('app.survey_service')->getSopRespondentId($user->getId());
        if ($app_mid != $app_mid2) {
            throw new \InvalidArgumentException("cint app_mid: {$app_mid} doesn't match its user_id: {$user->getId()}");
        }
        if ($this->get('app.cint_survey_service')->isFakedAnswer($survey_id, $app_mid)) {
            $this->get('logger')->info("a faked cint answer occurs, survey_id: {$survey_id}, app_mid: {$app_mid}");
            $answer_status = SurveyStatus::STATUS_SCREENOUT;
        }
        $this->get('app.cint_survey_service')->processSurveyEndlink(
            $survey_id,
            $tid,
            $user,
            $answer_status,
            $app_mid,
            $request->getClientIp()
        );
        $point = $this->get('app.cint_survey_service')->getSurveyPoint($app_mid, $survey_id);
        return $this->redirect($this->generateUrl('_cint_project_survey_endpage', array(
            'answer_status' => $answer_status,
            'survey_id' => $survey_id,
            'point' => $point,
        )));
    }

    /**
     * @Route("/endpage", name="_cint_project_survey_endpage")
     */
    public function endlinkPageAction(Request $request) {
        return $this->render('WenwenFrontendBundle:ProjectSurveyCint:endlink.html.twig', array(
            'answer_status' => $request->query->get('answer_status'),
            'survey_id' => $request->query->get('survey_id'),
            'point' => $request->query->get('point'),
        ));
    }
}