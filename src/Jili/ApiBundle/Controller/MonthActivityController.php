<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


class MonthActivityController extends Controller {

    /**
     * @Route("/july", name="_monthActivity_julyActivity")
     */
    public function julyActivityAction() {

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
        $start = "2014-05-01";
        $end = "2014-05-31";
        $request = $this->get('request');
        $user_id = $request->getSession()->get('uid');
        $my_point = 0;
        if ($user_id) {
            $em = $this->getDoctrine()->getManager();
            $myInfo = $em->getRepository('JiliApiBundle:User')->getSingleUserPointForJulyActivity($start, $end, $user_id);
            if($myInfo){
                $my_point = $myInfo[0]['points'];
            }
        }

        $users = array_chunk($users,50);

        return $this->render('JiliApiBundle:MonthActivity:julyActivity.html.twig', array (
            'users' => $users,'my_point' => $my_point
        ));
    }
}