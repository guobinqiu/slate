<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class MonthActivityController extends Controller
{
    /**
     * @Route("/july", name="_monthActivity_julyActivity")
     */
    public function julyActivityAction()
    {
        $filename = $this->container->getParameter('file_path_july_activity');
        //写文件
        $handle = fopen($filename, "r");
        $users = array ();
        if ($handle !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $users[] = $data;
            }
        }
        fclose($handle);
        $start = "2014-07-01 00:00:00";
        $end = "2014-07-31 23:59:59";
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

        return $this->render('JiliApiBundle:MonthActivity:julyActivity.html.twig', array (
            'users' => $users,
            'my_point' => $my_point
        ));
    }

    public function divideIntoGroups($users)
    {
        $users = array_chunk($users, 50);
        $users_right[] = $users[0][49]; //第50名
        $users_right[] = $users[1][49]; //第100名
        $users_right = array_merge($users_right, $users[2]);
        $users[2] = $users_right;
        return $users;
    }
}
