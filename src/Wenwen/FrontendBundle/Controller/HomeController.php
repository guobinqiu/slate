<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Entity\User;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\FrontendBundle\Entity\SurveyListJob;

class HomeController extends BaseController
{
    public function indexAction(Request $request)
    {
        $cookies = $request->cookies;
        if ($cookies->has(User::REMEMBER_ME_TOKEN)) {
            $user = $this->getDoctrine()->getRepository('WenwenFrontendBundle:User')->findOneBy(array('rememberMeToken' => $cookies->get(User::REMEMBER_ME_TOKEN)));
            if ($user != null && !$user->isRememberMeTokenExpired()) {
                $request->getSession()->set('uid', $user->getId());
            }
        }

        $session = $request->getSession();

        if (!$session->has('uid')) {
            $this->setRegisterRouteInSession($request);
            return $this->render('WenwenFrontendBundle:Home:index.html.twig');
        }

        $userId = $request->getSession()->get('uid');

        if ($session->has('referer')) {
            $url = $session->get('referer');
            $session->remove('referer');
            return $this->redirect($url);
        }

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
        $htmlSurveyList = $surveyService->getOrderedHtmlSurveyList($userId, $locationInfo);

        $this->checkoutSurveyList($userId);

        return $this->render('WenwenFrontendBundle:Home:home.html.twig', array(
            'html_survey_list' => $htmlSurveyList,
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
