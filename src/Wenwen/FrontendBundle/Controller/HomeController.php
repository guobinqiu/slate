<?php

namespace Wenwen\FrontendBundle\Controller;

use Jili\ApiBundle\Entity\Vote;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Entity\User;
use JMS\JobQueueBundle\Entity\Job;
use Wenwen\FrontendBundle\Entity\SurveyListJob;

class HomeController extends BaseController
{
    # 中国時間で日付変わる前に新規QSに答えるとボーナスポイント+1
    const RECENT_BONUS_HOUR = 24;
    const RECENT_BONUS_POINT = 5;

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

        $htmlSurveyList = $this->getHtmlSurveyList($request, $userId);

        //$this->checkoutSurveyList($userId);

        $latestNews = $this->get('app.latest_news_service')->getLatestNews();
        $em = $this->getDoctrine()->getManager();
        $callboard = $em->getRepository('JiliApiBundle:Callboard')->getCallboardLimit(4);

        //get vote list
        $result = $em->getRepository('JiliApiBundle:Vote')->fetchVoteList(true, 5);
        $vote_list = $this->getVoteData($result, $userId);

        return $this->render('WenwenFrontendBundle:Home:home.html.twig', array(
            'html_survey_list' => $htmlSurveyList,
            'latestNews' => $latestNews,
            'callboard' => $callboard,
            'vote_list' => $vote_list,
            'user' => $this->getCurrentUser(),
        ));
    }

    public function surveyListAction(Request $request)
    {
        if (!$this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $htmlSurveyList = $this->getHtmlSurveyList($request, $this->getCurrentUserId());

        return $this->render('WenwenFrontendBundle:Survey:_sopSurveyListHome.html.twig', array(
            'html_survey_list' => $htmlSurveyList,
        ));
    }

    private function getHtmlSurveyList(Request $request, $userId)
    {
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
        return $htmlSurveyList;
    }

    private function getVoteData($votes, $user_id)
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($votes as $key => $value) {
            //get vote answer count
            $votes[$key]['answerCount'] = $em->getRepository('JiliApiBundle:VoteAnswer')->getAnswerCount($value['id']);

            //get user answer count
            if ($value['endTime']->format('Y-m-d H:i:s') < date('Y-m-d H:i:s')) {
                $votes[$key]['answerable'] = false;
            } elseif ($user_id) {
                $count = $em->getRepository('JiliApiBundle:VoteAnswer')->getUserAnswerCount($user_id, $value['id']);
                $votes[$key]['answerable'] = $count ? false : true;
            } else {
                $votes[$key]['answerable'] = true;
            }

            if ($votes[$key]['voteImage']) {
                //get sq image path
                $vote = new Vote();
                $vote->setSrcImagePath($votes[$key]['voteImage']);
                $votes[$key]['sqPath'] = $this->container->getParameter('upload_vote_image_dir') . $vote->getDstImagePath('s');
            } else {
                $votes[$key]['sqPath'] = false;
            }

            //BonusHour
            if ($this->isInBonusHour($value['startTime'])) {
                $votes[$key]['timelimit'] = $this->getBonusTimeLimitDt($value['startTime'])->getTimestamp();
            }
        }

        return $votes;
    }

    private function isInBonusHour($start_time)
    {
        $dt = new \DateTime();
        $time_limit_dt = $this->getBonusTimeLimitDt($start_time);

        if ($dt < $time_limit_dt) {
            return true;
        }

        return false;
    }

    private function getBonusTimeLimitDt($start_time)
    {
        $start_time->modify(sprintf('+%d hour', self::RECENT_BONUS_HOUR));

        return $start_time;
    }

//    private function checkoutSurveyList($userId)
//    {
//        $timeslot = SurveyListJob::getTimeslot(time());
//        $min = new \DateTime($timeslot['min']);
//        $max = new \DateTime($timeslot['max']);
//        $em = $this->getDoctrine()->getManager();
//        $surveyListJob = $em->getRepository('WenwenFrontendBundle:SurveyListJob')->getSurveyListJob($userId, $min, $max);
//        if ($surveyListJob == null) {
//            $surveyListJob = new SurveyListJob($userId);
//            $em->persist($surveyListJob);
//            $em->flush();
//
//            //gmo
//            $surveyGmoService = $this->get('app.survey_gmo_service');
//            $researches = $surveyGmoService->getSurveyList($userId);
//            foreach ($researches as $research) {
//                $survey = $surveyGmoService->createOrUpdateSurvey($research);
//                $surveyGmoService->createParticipationByUserId($userId, $survey->getId(), SurveyStatus::STATUS_TARGETED);
//            }
//
//            //sop
//            $args = array(
//                '--user_id=' . $userId,
//            );
//            $job = new Job('sop:checkout_survey_list', $args, true, '91wenwen_sop');
//            $job->setMaxRetries(3);
//            $em->persist($job);
//            $em->flush();
//        }
//    }
}
