<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Acl\Exception\Exception;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * @Route("/survey_gmo")
 */
class SurveyGmoController extends BaseController
{
    private static $statusHash = array(
        1 => SurveyStatus::STATUS_COMPLETE,
        2 => SurveyStatus::STATUS_SCREENOUT,
        3 => SurveyStatus::STATUS_QUOTAFULL,
    );

    /**
     * @Route("/information", name="survey_gmo_information")
     */
    public function informationAction(Request $request)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $research = $request->query->get('research');
        $participation = $this->get('app.survey_gmo_service')->createParticipationByUserId(
            $this->getCurrentUserId(),
            $research['research_id'],
            SurveyStatus::STATUS_INIT,
            $request->getClientIp()
        );
        $em = $this->getDoctrine()->getManager();
        $participation->setUpdatedAt(new \DateTime());
        $em->flush();
        return $this->render('WenwenFrontendBundle:SurveyGmo:information.html.twig', array('research' => $research));
    }

    /**
     * @Route("/forward", name="survey_gmo_forward")
     */
    public function forwardAction(Request $request)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }
        $research = $request->query->get('research');
        $participation = $this->get('app.survey_gmo_service')->createParticipationByUserId(
            $this->getCurrentUserId(),
            $research['research_id'],
            SurveyStatus::STATUS_FORWARD,
            $request->getClientIp()
        );
        $em = $this->getDoctrine()->getManager();
        $participation->setUpdatedAt(new \DateTime());
        $em->flush();
        return $this->redirect($research['url']);
    }

    /**
     * @Route("/gp",name="survey_gmo_endlink", methods={"POST"})
     */
    public function endlinkAction(Request $request)
    {
        $ip = $request->getClientIp();
        $ip = '127.0.0.1';//todo
        if ('127.0.0.1' === $ip || preg_match('/^210\.172\.135\.\d{1,3}$/', $ip)) {
            $memberId = $request->request->get('member_id');
            $point = $request->request->get('point');
            $surveyId = $request->request->get('survey_id');
            $surveyName = $request->request->get('survey_name');
            $grantTimes = $request->request->get('grant_times');
            $status = $request->request->get('status');
            try {
                $this->get('app.survey_gmo_service')->processSurveyEndlink(
                    $surveyId,
                    $memberId,
                    self::$statusHash[$status],
                    $point,
                    $request->getClientIp()
                );
                return new Response(0);
            } catch (\Exception $e) {
                return new Response(2);
            }
        }
        return new AccessDeniedHttpException();
    }

//    /**
//     * @Route("/endpage", name="survey_gmo_endpage")
//     */
//    public function endlinkPageAction(Request $request)
//    {
//        return $this->render('WenwenFrontendBundle:SurveyGmo:endlink.html.twig', array(
//            'answer_status' => $request->query->get('answer_status'),
//            'survey_id' => $request->query->get('survey_id'),
//            'point' => $request->query->get('point'),
//        ));
//    }
}
