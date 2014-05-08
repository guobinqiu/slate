<?php
namespace Jili\ApiBundle\Controller;
use Jili\ApiBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(requirements={"_scheme"="http"})
 */
class TopCronController extends Controller {

    /**
     * @Route("/recentPoint", name="_topcron_recentPoint")
     */
    public function recentPointAction()
    {

        $em = $this->getDoctrine()->getManager();

        //最新动态
        $yesterday =  date("Y-m-d", strtotime(' -1 day'));
        $newActivity = $em->getRepository('JiliApiBundle:User')->getRecentPoint($yesterday);

        $filename = $this->container->getParameter('file_path_recent_point');
//        if (!file_exists($filename)) {
//            die("指定文件不存在，操作中断!");
//        }
        //写文件
        $handle = fopen($filename, "w");
        if (!$handle) {
            die("指定文件不能打开，操作中断!");
        }
        foreach ($newActivity as $row) {
            $line = $row['nick'].",".$row['icon_path'].",".$row['point_change_num'].",".$row['display_name']."\n";
            fwrite($handle, $line);
        }
        fclose($handle);

        return new Response(200);

    }

    /**
     * @Route("/rankingMonth", name="_topcron_rankingMonth")
     */
    public function rankingMonthAction()
    {
        $em = $this->getDoctrine()->getManager();

        $start = date("Y-m-d", strtotime(' -30 day'))." 00:00:00";
        $end = date("Y-m-d", strtotime(' -1 day'))." 23:59:59";
        $ranking = $em->getRepository('JiliApiBundle:User')->getRanking($start,$end);

        $filename = $this->container->getParameter('file_path_ranking_month');
//        if (!file_exists($filename)) {
//            die("指定文件不存在，操作中断!");
//        }
        //写文件
        $handle = fopen($filename, "w");
        if (!$handle) {
            die("指定文件不能打开，操作中断!");
        }
        foreach ($ranking as $row) {
            $line = $row['nick'].",".$row['total']."\n";
            fwrite($handle, $line);
        }
        fclose($handle);

        return new Response(200);

    }

    /**
     * @Route("/rankingYear", name="_topcron_rankingYear")
     */
    public function rankingYearAction()
    {
        $em = $this->getDoctrine()->getManager();

        $start = date("Y-m-d", strtotime(' -365 day'))." 00:00:00";
        $end = date("Y-m-d", strtotime(' -1 day'))." 23:59:59";
        $ranking = $em->getRepository('JiliApiBundle:User')->getRanking($start,$end);

        $filename = $this->container->getParameter('file_path_ranking_year');
//        if (!file_exists($filename)) {
//            die("指定文件不存在，操作中断!");
//        }
        //写文件
        $handle = fopen($filename, "w");
        if (!$handle) {
            die("指定文件不能打开，操作中断!");
        }
        foreach ($ranking as $row) {
            $line = $row['nick'].",".$row['total']."\n";
            fwrite($handle, $line);
        }

        fclose($handle);

        return new Response(200);

    }

}
