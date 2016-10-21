<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Acl\Exception\Exception;
use Wenwen\AppBundle\Entity\CintUserAgreementParticipationHistory;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

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
        if (!$auth->verifySignature($sig, $params)) {
            throw new Exception('签名验证失败');
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
            $this->get('app.user_service')->addPoints(
                $user,
                self::AGREEMENT_POINT,
                CategoryType::CINT_EXPENSE,
                TaskType::RENTENTION,
                self::COMMENT
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
        $sop_custom_token = uniqid();
        $request->getSession()->set('sop_custom_token', $sop_custom_token);

        $cint_research = $request->query->get('cint_research');
        $cint_research['url'] = $this->get('app.survey_service')
            ->urlAddExtraParameters($cint_research['url'], array('sop_custom_token' => $sop_custom_token));

        return $this->render('WenwenFrontendBundle:ProjectSurveyCint:information.html.twig', array(
            'cint_research' => $cint_research
        ));
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_cint_project_survey_endlink")
     */
    public function endlinkAction(Request $request)
    {
        $anwerStatus = $request->query->get('answer_status');
        $tid = $request->query->get('tid');
        $sop_custom_token = $request->getSession()->get('sop_custom_token');
        if ($sop_custom_token == $tid) {
            // 获得一次抽奖机会
            $this->get('app.prize_service')->createPrizeTicketForResearchSurvey(
                $this->getCurrentUser(),
                $anwerStatus,
                'cint商业问卷' . $anwerStatus
            );

            //防止通过反复刷页面来进行作弊
            $request->getSession()->set('sop_custom_token', uniqid());
        }

        return $this->render('WenwenFrontendBundle:ProjectSurveyCint:endlink.html.twig', array(
            'answer_status' => $anwerStatus,
            'survey_id' => $request->get('survey_id'),
        ));
    }
}
