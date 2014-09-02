<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Jili\ApiBundle\Utility\FileUtil;
use Jili\ApiBundle\Utility\DateUtil;

class MonthActivityController extends Controller
{
    /**
     * @Route("/july", name="_monthActivity_julyActivity")
     */
    public function julyActivityAction()
    {
        return $this->redirect($this->generateUrl('_monthActivity_cparanking',array('month'=>7)));
    }

    /**
     * @Route("/cparanking/{month}", name="_monthActivity_cparanking")
     */
    public function cpaRankingActivityAction($month)
    {
        $filename = $this->container->getParameter('file_path_cpa_ranking_activity');
        $users = FileUtil :: readCsvContent($filename);

        $date = DateUtil :: getTimeByMonth($month);
        $start = $date['start_time'];
        $end = $date['end_time'];

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

        return $this->render('JiliApiBundle:MonthActivity:cpaRankingActivity.html.twig', array (
            'users' => $users,
            'my_point' => $my_point
        ));
    }

    public function divideIntoGroups($users) {
        $users = array_chunk($users, 50);
        $users_right[] = $users[0][49]; //第50名
        $users_right[] = $users[1][49]; //第100名
        $users_right = array_merge($users_right, $users[2]);
        $users[2] = $users_right;
        return $users;
    }
}