<?php

namespace Wenwen\FrontendBundle\Controller;

use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wenwen\FrontendBundle\Entity\SurveyListJob;
use Wenwen\FrontendBundle\Model\SurveyStatus;

/**
 * @Route("/survey")
 */
class SurveyController extends BaseController implements UserAuthenticationController
{
    /**
     * @Route("/index", name="_survey_index")
     */
    public function indexAction(Request $request)
    {
        return $this->redirect($this->generateUrl('_homepage'));
    }

    /**
     * @Route("/top", name="_survey_top")
     */
    public function topAction(Request $request)
    {
        $user_id = $request->getSession()->get('uid');

        $this->get('logger')->debug(__METHOD__ . ' ' . $request->getClientIp());

        // 根据Ip获取该用户的地区信息
        $locationInfo = $this->getLocationInfoByClientIp($request);

        // 处理ssi和sop的排序，排序列表里存的是一个个通过模板渲染出来的html片段，每种模板分别对应一类问卷
        $surveyService = $this->get('app.survey_service');
        $env = $this->container->get('kernel')->getEnvironment();
        if (in_array($env, array('dev','test'))) {
            // for dummy mode (won't access sop's server at dev or test mode)
            // test环境时不去访问SOP服务器，在circleCI上运行测试case时，访问SOP服务器会超时，导致测试运行极慢
            $surveyService->setDummy(true);
        }
        $html_survey_list = $surveyService->getOrderedHtmlSurveyList($user_id, $locationInfo);

        $this->checkoutSurveyList($user_id);

        return $this->render('WenwenFrontendBundle:Survey:_sopSurveyListHome.html.twig', array(
            'html_survey_list' => $html_survey_list,
        ));
    }

    private function checkoutSurveyList($userId)
    {
        $timeslot = SurveyListJob::getTimeslot(time());
        $min = new \DateTime($timeslot['min']);
        $max = new \DateTime($timeslot['max']);
        $em = $this->getDoctrine()->getManager();
        $surveyListJob = $em->getRepository('WenwenFrontendBundle:SurveyListJob')->getSurveyListJob($userId, $min, $max);
        if ($surveyListJob == null) {
            $surveyListJob = new SurveyListJob($userId);
            $em->persist($surveyListJob);
            $em->flush();

            //gmo
            $surveyGmoService = $this->get('app.survey_gmo_service');
            $researches = $surveyGmoService->getSurveyList($userId);
            foreach ($researches as $research) {
                $survey = $surveyGmoService->createOrUpdateSurvey($research);
                $surveyGmoService->createParticipationByUserId($userId, $survey->getId(), SurveyStatus::STATUS_TARGETED);
            }

            //sop
            $args = array(
                '--user_id=' . $userId,
            );
            $job = new Job('sop:checkout_survey_list', $args, true, '91wenwen_sop');
            $job->setMaxRetries(3);
            $em->persist($job);
            $em->flush();
        }
    }
}
