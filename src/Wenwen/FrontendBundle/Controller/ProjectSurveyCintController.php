<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\ApiBundle\Entity\AdCategory;
use Jili\ApiBundle\Entity\TaskHistory00;
use Wenwen\AppBundle\Entity\CintUserAgreementParticipationHistory;
/**
 * @Route("/cint_project_survey")
 */
class ProjectSurveyCintController extends Controller
{
    const AGREEMENT_POINT = 1;
    const TYPE_EXPENSE = AdCategory::ID_QUESTIONNAIRE_EXPENSE;
    const TASK_TYPE_ID = TaskHistory00::TASK_TYPE_SURVEY;
    const COMMENT = '同意Cint问卷';

    /**
     * @Route("/agreement_complete", name="_cint_project_survey_agreement_complete")
     */
    public function agreementCompleteAction(Request $request)
    {
        if (!$request->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $request->getUri());
            return $this->redirect($this->generateUrl('_user_login'));
        }

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
            $response = new Response();
            $response->setStatusCode(404);
            return $this->render('WenwenFrontendBundle:Exception:index.html.twig', array (), $response);
        }

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
            $service = $this->container->get('points_manager');
            $service->updatePoints($user_id, self::AGREEMENT_POINT, self::TYPE_EXPENSE, self::TASK_TYPE_ID, self::COMMENT);

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
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('referer', $request->getUri());
            return $this->redirect($this->generateUrl('_user_login'));
        }

        return $this->render('WenwenFrontendBundle:ProjectSurveyCint:information.html.twig', array('cint_research' => $request->query->get('cint_research')));
    }

    /**
     * @Route("/endlink/{survey_id}/{answer_status}", name="_cint_project_survey_endlink")
     */
    public function endlinkAction(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $answer_status = $request->get('answer_status');

        if (!$request->getSession()->get('uid')) {
            $this->get('request')->getSession()->set('referer', $request->getUri());
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $arr['answer_status'] = $answer_status;
        $arr['survey_id'] = $survey_id;
        return $this->render('WenwenFrontendBundle:ProjectSurveyCint:complete.html.twig', $arr);
    }
}
