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
class ProjectSurveyCintController extends BaseController
{
    const AGREEMENT_POINT = 10;
    const COMMENT = '同意参与海外市场调查项目';

    /**
     * @Route("/agreement_complete", name="_cint_project_survey_agreement_complete")
     */
    public function agreementCompleteAction(Request $request)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $user = $this->getCurrentUser();
        $em = $this->getDoctrine()->getManager();

        $history = $em->getRepository('WenwenAppBundle:CintUserAgreementParticipationHistory')->findOneByUserId($user->getId());
        if ($history) {
            return $this->render('WenwenFrontendBundle:ProjectSurveyCint:agreementComplete.html.twig');
        }

        $params = $request->query->all();

        $cint_config = $this->container->getParameter('cint');
        $status_map = $cint_config['user_agreement'];

        // Verify signature
        $sopRespondent = $this->get('app.user_service')->getSopRespondentByUserId($user->getId());
        $sopCredentials = $this->get('app.survey_sop_service')->getSopCredentialsByAppId($sopRespondent->getAppId());
        $appId = $sopCredentials['app_id'];
        $appSecret = $sopCredentials['app_secret'];
        $auth = new \SOPx\Auth\V1_1\Client($appId, $appSecret);
        $sig = $params['sig'];
        unset($params['sig']);

        $result = $auth->verifySignature($sig, $params);

        if (!$result['status']) {
            $this->container->get('logger')->error(__METHOD__ . ' errMsg='.$result['msg']);
            return new Response('authentication failed', 400);
        }

        // start transaction
        $em->getConnection()->beginTransaction();

        try {
            // insert history
            $history_model = new CintUserAgreementParticipationHistory();
            $history_model->setUserId($user->getId());
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
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $cint_research = $request->query->get('cint_research');
        $participation = $this->get('app.survey_cint_service')->createParticipationByUserId(
            $this->getCurrentUserId(),
            $cint_research['survey_id'],
            SurveyStatus::STATUS_INIT,
            $request->getClientIp()
        );
        $em = $this->getDoctrine()->getManager();
        $participation->setUpdatedAt(new \DateTime());
        $em->flush();
        return $this->render('WenwenFrontendBundle:ProjectSurveyCint:information.html.twig', array('cint_research' => $cint_research));
    }

    /**
     * @Route("/forward", name="_cint_project_survey_forward")
     */
    public function forwardAction(Request $request)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $cint_research = $request->query->get('cint_research');
        $participation = $this->get('app.survey_cint_service')->createParticipationByUserId(
            $this->getCurrentUserId(),
            $cint_research['survey_id'],
            SurveyStatus::STATUS_FORWARD,
            $request->getClientIp()
        );
        $em = $this->getDoctrine()->getManager();
        $participation->setUpdatedAt(new \DateTime());
        $em->flush();
        $cint_research = $this->get('app.survey_cint_service')->addSurveyUrlToken($cint_research, $this->getCurrentUserId());
        return $this->redirect($cint_research['url']);
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_cint_project_survey_endlink")
     */
    public function endlinkAction(Request $request, $survey_id, $answer_status)
    {
        $tid = $request->query->get('tid');
        $app_mid = $request->query->get('app_mid');
        $point = $this->get('app.survey_cint_service')->processSurveyEndlink(
            $survey_id,
            $tid,
            $app_mid,
            $answer_status,
            $request->getClientIp()
        );
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