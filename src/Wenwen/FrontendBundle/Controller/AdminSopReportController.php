<?php

namespace Wenwen\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\BackendBundle\Controller\IpAuthenticatedController;

/**
 * @Route("/admin/report")
 */
class AdminSopReportController extends BaseController implements IpAuthenticatedController
{
    /**
     * @Route("/sop/participation", name="admin_report_sop_participation")
     */
    public function adminReportSopParticipationAction(Request $request)
    {
        $sql = "
            select sb.*,
            round(sb.forward / sb.init * 100, 2) cvr1,
            round(sb.total / sb.forward * 100, 2) cvr2
            from (
              select date_format(created_at, '%Y-%m-%d') date,
              sum(case when status in ('complete', 'screenout', 'quotafull', 'error') then 1 else 0 end) as total,
              sum(case status when 'init' then 1 else 0 end) as init,
              sum(case status when 'forward' then 1 else 0 end) as forward,
              sum(case status when 'complete' then 1 else 0 end) as complete,
              sum(case status when 'screenout' then 1 else 0 end) as screenout,
              sum(case status when 'quotafull' then 1 else 0 end) as quotafull,
              sum(case status when 'error' then 1 else 0 end) as error
              from survey_sop_participation_history
              where date_sub(curdate(), interval 30 day) <= date(created_at)
              group by date(created_at)
              order by created_at desc
            ) as sb;
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();

//        $serializer = $this->get('jms_serializer');
//        return new Response($serializer->serialize($result, 'json'));
        return $this->render('WenwenFrontendBundle:admin:adminReportSopParticipation.html.twig', array('result' => $result));
    }

    //问卷项目一览
    //问卷ID => survey_xxx.id
    //survey_id =>  survey_xxx.survey_id
    //问卷title => survey_xxx.title
    //问卷的状态 => 进行中/结束
    /**
     * @Route("/sop/surveylist", name="admin_report_sop_surveylist")
     */
    public function adminReportSopSurveyListAction(Request $request)
    {

//        $allSurveys = $this->getDoctrine()->getRepository('WenwenFrontendBundle:SurveySop')->findAll();
//        $serializer = $this->get('jms_serializer');
//        return new Response($serializer->serialize($allSurveys, 'json'));
        $sql = "select * from survey_sop";
        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();
        $pagination = $this->get('knp_paginator')->paginate($result, $request->query->getInt('page', 1), 100);
        return $this->render('WenwenFrontendBundle:admin:adminReportSopSurveyList.html.twig', array('pagination' => $pagination));
    }

    //每个问卷的详细情况
    //2-1）survey_xxx的所有内容。
    //2-2）各个状态的计数，target/init/forward/C/S/Q/E
    //2-3）CVR(init->forward) = forward/init
    //2-4）CVR(forward->CSQE) = CSQE/forward
    //2-5）Total LOI = average(Complete - forward的时间)
    //2-6）实际LOI= average(CSQE的时间 - forward的时间)
    //2-7）可以查看/导出选定状态（CSQE）的survey_xxx_participation_history的内容
    //Scr LOI = average(SQE - forward的时间)

    //每日的统计里面还有增加的需求，
    //1，每天的总的可回答问卷数。（问卷开始时间<= 当日 <问卷结束时间）
    //2，每天增加的新问卷数。（当日0点 <= 问卷的开始时间 < 次日0点）

}
