<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Wenwen\FrontendBundle\Entity\GmoGrantPointHistory;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * @Route("/survey_gmo")
 */
class SurveyGmoController extends BaseController
{
    const SUCCESS = 0;
    const INVALID_PARAMETER = 1;
    const INNER_ERROR = 2;
    const DUPLICATE_ANSWER = 3;

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
     * @Route("/gp",name="survey_gmo_grant_point", methods={"POST"})
     */
    public function grantPointAction(Request $request)
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
                $history = new GmoGrantPointHistory();
                $history->setMemberId($memberId);
                $history->setPoint($point);
                $history->setSurveyId($surveyId);
                $history->setSurveyName($surveyName);
                $history->setGrantTimes($grantTimes);
                $history->setStatus($status);
                $em = $this->getDoctrine()->getManager();
                $em->persist($history);
                $em->flush();
            } catch (\Exception $e) {
                return new Response(self::DUPLICATE_ANSWER);
            }

            try {
                $this->get('app.survey_gmo_service')->processSurveyEndlink(
                    $surveyId,
                    $memberId,
                    self::$statusHash[$status],
                    $point,
                    $request->getClientIp()
                );
                return new Response(self::SUCCESS);
            } catch (\Exception $e) {
                return new Response(self::INNER_ERROR);
            }
        }
        return new AccessDeniedHttpException();
    }

    /**
     * @Route("/csv",name="survey_gmo_csv", methods={"GET"})
     */
    public function downloadMemberListCSV()
    {
        $filepath = $this->get('app.parameter_service')->getParameter('gmo_memberlist_filepath');
        $filename = $this->get('app.parameter_service')->getParameter('gmo_memberlist_filename');
        $file = $filepath . '/' . $filename . '.zip';
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        return $response;
    }

//    /**
//     * @Route("/endlink/{survey_id}/{answer_status}", name="survey_gmo_endlink")
//     */
//    public function endlinkAction(Request $request, $survey_id, $answer_status)
//    {
//        $point = $request->query->get('point');
//        return $this->redirect($this->generateUrl('survey_gmo_endpage', array(
//            'answer_status' => $answer_status,
//            'survey_id' => $survey_id,
//            'point' => $point,
//        )));
//    }

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
