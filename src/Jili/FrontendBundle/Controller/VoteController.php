<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Jili\ApiBundle\Entity\Vote;
use Jili\ApiBundle\Entity\VoteAnswer;
use Jili\ApiBundle\Entity\AdCategory;
use Jili\FrontendBundle\Form\VoteSuggestType;

/**
 * @Route("/vote",requirements={"_scheme"="http"})
 */
class VoteController extends Controller
{
    # 中国時間で日付変わる前に新規QSに答えるとボーナスポイント+1
    const RECENT_BONUS_HOUR = 24;

    const RECENT_BONUS_POINT = 1;

    /**
     * @Route("/top")
     * @Template
     */
    public function topAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //get vote list
        $result = $em->getRepository('JiliApiBundle:Vote')->fetchVoteList(true, 5);

        $user_id = $request->getSession()->get('uid');
        $result = $this->getVoteData($result, $user_id);

        $arr['pagination'] = $result;
        return $this->render('JiliFrontendBundle:Vote:top.html.twig', $arr);
    }

    /**
     * @Route("/index")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $page = $request->query->get('page', 1);
        $page_size = $this->container->getParameter('page_num');
        $em = $this->getDoctrine()->getManager();

        //get vote list
        $result = $em->getRepository('JiliApiBundle:Vote')->fetchVoteList();
        $user_id = $request->getSession()->get('uid');
        $result = $this->getVoteData($result, $user_id);

        // 分页显示
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator->paginate($result, $page, $page_size);
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');

        $arr['page'] = $page;

        return $this->render('JiliFrontendBundle:Vote:index.html.twig', $arr);
    }

    public function getVoteData($votes, $user_id)
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($votes as $key => $value) {
            //get vote answer count
            $votes[$key]['answerCount'] = $em->getRepository('JiliApiBundle:VoteAnswer')->getAnswerCount($value['id']);

            //get user answer count
            if ($user_id) {
                $votes[$key]['userAnswerCount'] = $em->getRepository('JiliApiBundle:VoteAnswer')->getUserAnswerCount($user_id, $value['id']);
            } else {
                $votes[$key]['userAnswerCount'] = 0;
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

    /**
     * @Route("/show")
     */
    public function showAction(Request $request)
    {
        $current_timestamp = time();
        $em = $this->getDoctrine()->getManager();

        $vote_id = $request->query->get('id');
        if (!$vote_id) {
            return $this->redirect($this->generateUrl('_default_error'));
        }

        $vote = $em->getRepository('JiliApiBundle:Vote')->findOneById($vote_id);

        # voteが無い/開始前 => 404
        if (!$vote || $vote->getStartTime()->getTimestamp() > $current_timestamp) {
            return $this->redirect($this->generateUrl('_default_error'));
        }

        # voteが終了済み => 結果へ
        if ($vote->getEndTime()->getTimestamp() < $current_timestamp) {
            return $this->redirect($this->generateUrl('jili_frontend_vote_result', array (
                'id' => $vote_id
            )));
        }

        $user_answer_count = 0;
        $user_id = $request->getSession()->get('uid');
        if ($user_id) {
            $user_answer_count = $em->getRepository('JiliApiBundle:VoteAnswer')->getUserAnswerCount($user_id, $vote_id);
            # 回答済み => 結果へ
            if ($user_answer_count) {
                return $this->redirect($this->generateUrl('jili_frontend_vote_result', array (
                    'id' => $vote_id
                )));
            }
        }

        $vote_image_path = false;
        if ($vote->getVoteImage()) {
            $vote->setSrcImagePath($vote->getVoteImage());
            $vote_image_path = $this->container->getParameter('upload_vote_image_dir') . $vote->getDstImagePath('s');
        }

        $stashData = $vote->getStashData();
        $voteChoices = $stashData['choices'];

        $arr['vote'] = $vote;
        $arr['user_answer_count'] = $user_answer_count;
        $arr['vote_image_path'] = $vote_image_path;
        $arr['voteChoices'] = $voteChoices;

        $csrfProvider = new DefaultCsrfProvider('SECRET');
        $csrf_token = $csrfProvider->generateCsrfToken('vote');
        $session = $request->getSession();
        $session->set('csrf_token', $csrf_token);
        $arr['csrf_token'] = $csrf_token;

        return $this->render('JiliFrontendBundle:Vote:show.html.twig', $arr);
    }

    /**
     * @Route("/vote")
     */
    public function voteAction(Request $request)
    {
        //check post
        if ($request->getMethod() != 'POST') {
            return $this->redirect($this->generateUrl('jili_frontend_vote_index'));
        }

        //check login
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('referer', $this->generateUrl('jili_frontend_vote_index'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $vote_id = $request->request->get('id');
        $answer_number = $request->request->get('answer_number');

        //check parameter vote_id
        if (!$vote_id) {
            return $this->redirect($this->generateUrl('jili_frontend_vote_index'));
        }

        //check parameter answer_number
        if (!$answer_number) {
            return $this->redirect($this->generateUrl('jili_frontend_vote_show', array (
                'id' => $vote_id
            )));
        }

        //check csrf_token
        $session = $request->getSession();
        $csrf_token = $request->request->get('csrf_token');
        if (!$csrf_token || ($csrf_token != $session->get('csrf_token'))) {
            return $this->redirect($this->generateUrl('jili_frontend_vote_result', array (
                'id' => $vote_id
            )));
        }

        //get vote
        $em = $this->getDoctrine()->getManager();
        $vote = $em->getRepository('JiliApiBundle:Vote')->findOneById($vote_id);

        //check vote exist
        if (!$vote) {
            return $this->redirect($this->generateUrl('jili_frontend_vote_index'));
        }

        //check answer_number
        $stashData = $vote->getStashData();
        if (!array_key_exists($answer_number, $stashData['choices'])) {
            return $this->redirect($this->generateUrl('jili_frontend_vote_show', array (
                'id' => $vote_id
            )));
        }

        //check answered
        $user_id = $this->get('session')->get('uid');
        $user_answer_count = $em->getRepository('JiliApiBundle:VoteAnswer')->getUserAnswerCount($user_id, $vote_id);
        if ($user_answer_count) {
            return $this->redirect($this->generateUrl('jili_frontend_vote_result', array (
                'id' => $vote_id
            )));
        }

        try {
            $db_connection = $em->getConnection();
            $db_connection->beginTransaction();

            $point = $this->calcRewardPoint($vote->getPointValue(), $vote->getStartTime());

            //insert vote answer
            $answer = new VoteAnswer();
            $answer->setUserId($user_id);
            $answer->setVoteId($vote_id);
            $answer->setAnswerNumber($answer_number);
            $em->persist($answer);

            // insert task_history
            $task_params = array (
                'userid' => $user_id,
                'orderId' => 0,
                'taskType' => \Jili\ApiBundle\Entity\TaskHistory00::TASK_TYPE_CHECKIN,
                'categoryType' => AdCategory::ID_QUESTIONNAIRE_EXPENSE,
                'task_name' => '快速问答',
                'point' => $point,
                'date' => date_create(),
                'status' => 1
            );
            $this->get('general_api.task_history')->init($task_params);

            // insert point_history
            $points_params = array (
                'userid' => $user_id,
                'point' => $point,
                'type' => AdCategory::ID_QUESTIONNAIRE_EXPENSE
            );

            $this->get('general_api.point_history')->get($points_params);

            // update user.point更新user表总分数
            $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
            $oldPoint = $user->getPoints();
            $user->setPoints(intval($oldPoint + $point));
            $em->persist($user);
            $em->flush();

            $db_connection->commit();
        } catch (\Exception $e) {
            $db_connection->rollback();
            $this->get('logger')->critical('[JiliFrontend][vote][click]' . $e->getMessage());
            $this->get('session')->getFlashBag()->add('error', '投票失败，内部出错');
            return $this->redirect($this->generateUrl('_default_error'));
        }
        $session->remove('csrf_token');
        return $this->redirect($this->generateUrl('jili_frontend_vote_result', array (
            'id' => $vote_id
        )));
    }

    /**
     * @Route("/result")
     */
    public function resultAction(Request $request)
    {
        $current_timestamp = time();
        $em = $this->getDoctrine()->getManager();

        $vote_id = $request->query->get('id');
        if (!$vote_id) {
            return $this->redirect($this->generateUrl('_default_error'));
        }

        $vote = $em->getRepository('JiliApiBundle:Vote')->findOneById($vote_id);

        # voteが無い/開始前 => 404
        if (!$vote || $vote->getStartTime()->getTimestamp() > $current_timestamp) {
            return $this->redirect($this->generateUrl('_default_error'));
        }

        $vote_image_path = false;
        if ($vote->getVoteImage()) {
            $vote->setSrcImagePath($vote->getVoteImage());
            $vote_image_path = $this->container->getParameter('upload_vote_image_dir') . $vote->getDstImagePath('s');
        }

        $stashData = $vote->getStashData();
        $voteChoices = $stashData['choices'];
        $answer_count = 0;
        $choices = array ();
        foreach ($voteChoices as $key => $value) {
            $choices[$key]['name'] = $value;
            $choices[$key]['answer_count'] = $em->getRepository('JiliApiBundle:VoteAnswer')->getEachAnswerCount($vote_id, $key);
            $answer_count = $answer_count + $choices[$key]['answer_count'];
        }

        $arr['vote'] = $vote;
        $arr['vote_image_path'] = $vote_image_path;
        $arr['choices'] = $choices;
        $arr['answer_count'] = $answer_count;

        return $this->render('JiliFrontendBundle:Vote:result.html.twig', $arr);
    }

    /**
     * @Route("/recommend")
     */
    public function recommendAction(Request $request)
    {
        $user_id = $this->get('session')->get('uid');
        if (!$user_id) {
            return $this->render('JiliFrontendBundle:Vote:recommend.html.twig', array ());
        }
        $em = $this->getDoctrine()->getManager();

        $votes = $em->getRepository('JiliApiBundle:Vote')->getActiveVoteList();
        foreach ($votes as $key => $vote) {
            $user_answer_count = $em->getRepository('JiliApiBundle:VoteAnswer')->getUserAnswerCount($user_id, $vote['id']);
            if ($user_answer_count > 0) {
                unset($votes[$key]);
            }
        }
        $arr['votes'] = $votes;
        return $this->render('JiliFrontendBundle:Vote:recommend.html.twig', $arr);
    }

    /**
     * @Route("/suggest")
     */
    public function suggestAction(Request $request)
    {
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('referer', $this->generateUrl('jili_frontend_vote_suggest'));
            return $this->redirect($this->generateUrl('_user_login'));
        }

        $send_ok = $request->query->get('send_ok', false);

        $form = $this->createForm(new VoteSuggestType());

        if ($request->getMethod() === 'POST') {

            $form->bind($request);

            if ($form->isValid()) {

                //send email
                $this->send($form->getData());

                return $this->redirect($this->generateUrl('jili_frontend_vote_suggest', array (
                    'send_ok' => true
                )));
            }
        }
        return $this->render('JiliFrontendBundle:Vote:suggest.html.twig', array (
            'form' => $form->createView(),
            'send_ok' => $send_ok
        ));
    }

    private function send($values)
    {
        $request = $this->get('request');
        $user_id = $request->getSession()->get('uid');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $user_email = $user->getEmail();
        $mail_to = $this->container->getParameter('vote_suggest_mail_to');
        $mailer_return_path = $this->container->getParameter('mailer_return_path');

        $engine = $this->container->get('templating');
        $content = $engine->render('JiliFrontendBundle:Vote:mailbody.html.twig', array (
            'email' => $user_email,
            'values' => $values
        ));

        $subject = '[QS] ' . $values['title'];
        $message = \Swift_Message::newInstance()
                        ->setSubject($subject)
                        ->setFrom($user_email)
                        ->setTo($mail_to)
                        ->setReturnPath($mailer_return_path)
                        ->setBody($content);

        $mailer = $this->container->get('mailer');
        $mailer->send($message);
    }

    public function calcRewardPoint($vote_point, $start_time)
    {
        # with time bonus
        if ($this->isInBonusHour($start_time)) {
            return ($vote_point + self::RECENT_BONUS_POINT);
        }

        # without bonus
        return $vote_point;
    }

    public function isInBonusHour($start_time)
    {
        $dt = new \DateTime();
        $time_limit_dt = $this->getBonusTimeLimitDt($start_time);

        if ($dt < $time_limit_dt) {
            return true;
        }
        return false;
    }

    public function getBonusTimeLimitDt($start_time)
    {
        $start_time->modify(sprintf('+%d hour', self::RECENT_BONUS_HOUR));

        return $start_time;
    }
}
