<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Utility\FileUtil;

class MonthActivityController extends Controller {

    /**
     * @Route("/july", name="_monthActivity_july")
     */
    public function julyActivityAction() {
        //7月
        $date['start_time'] = '2014-07-01 00:00:00';
        $date['end_time'] = '2014-07-31 23:59:59';
        $cpaRankingData = $this->getCpaRankingData($date);
        return $this->render('JiliApiBundle:MonthActivity:julyActivity.html.twig', array (
            'users' => $cpaRankingData['users'],
            'my_point' => $cpaRankingData['my_point']
        ));
    }

    /**
     * @Route("/september", name="_monthActivity_september")
     */
    public function septemberActivityAction() {
        //9月
        $date['start_time'] = '2014-09-01 00:00:00';
        $date['end_time'] = '2014-09-30 23:59:59';
        $cpaRankingData = $this->getCpaRankingData($date);
        return $this->render('JiliApiBundle:MonthActivity:septemberActivity.html.twig', array (
            'users' => $cpaRankingData['users'],
            'my_point' => $cpaRankingData['my_point']
        ));
    }

    /**
     * @Route("/october", name="_monthActivity_october")
     */
    public function octoberActivityAction() {
        //10月
        $date['start_time'] = '2014-10-15 00:00:00';
        $date['end_time'] = '2014-11-14 23:59:59';
        $cpaRankingData = $this->getCpaRankingData($date);
        return $this->render('JiliApiBundle:MonthActivity:octoberActivity.html.twig', array (
            'users' => $cpaRankingData['users'],
            'my_point' => $cpaRankingData['my_point']
        ));
    }

    public function getCpaRankingData($date) {
        $cpaRankingData = array ();

        $start = $date['start_time'];
        $end = $date['end_time'];

        //读文件
        $file_path = $this->container->getParameter('file_path_cpa_ranking_activity');
        $filename = $file_path . date('Ym', strtotime($start)) . '.csv';
        $users = FileUtil :: readCsvContent($filename);

        $request = $this->get('request');
        $user_id = $request->getSession()->get('uid');
        $my_point = 0;
        if ($user_id) {
            $em = $this->getDoctrine()->getManager();
            $myInfo = $em->getRepository('JiliApiBundle:User')->getUserCPAPointsByTime($start, $end, $user_id);
            if ($myInfo) {
                $my_point = $myInfo[0]['points'];
            }
        }

        //divide users into groups for display on page
        $users = $this->divideIntoGroups($users);

        $cpaRankingData['my_point'] = $my_point;
        $cpaRankingData['users'] = $users;

        return $cpaRankingData;
    }

    public function divideIntoGroups($users) {
        if (count($users) < 1) {
            return $users;
        }
        elseif (count($users) < 50) {
            $user[0] = $users;
            return $user;
        }
        $users = array_chunk($users, 50);
        $users_right = array ();
        if (isset ($users[0][49])) {
            $users_right[] = $users[0][49]; //第50名
        }
        if (isset ($users[1][49])) {
            $users_right[] = $users[1][49]; //第100名
        }
        if ($users_right && isset ($users[2])) {
            $users_right = array_merge($users_right, $users[2]);
        }
        $users[2] = $users_right;
        return $users;
    }

}
