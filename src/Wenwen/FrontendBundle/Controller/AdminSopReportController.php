<?php

namespace Wenwen\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\BackendBundle\Controller\IpAuthenticatedController;

/**
 * @Route("/admin/report")
 */
class AdminSopReportController extends BaseController #implements IpAuthenticatedController
{
    /**
     * @Route("/sop/daily_report", name="admin_report_sop_daily_report")
     */
    public function adminReportSopDailyReport(Request $request)
    {
        $sql = "
            select *,
            round(complete_count / csqe_count * 100, 2) as real_ir,
            round(forward_count / init_count * 100, 2) as cvr1,
            round(csqe_count / forward_count * 100, 2) as cvr2,
            round(csqe_count / targeted_count * 100, 2) as cvr3
            from (
              select date(created_at) as created_date,
              sum(case status when 'targeted' then 1 else 0 end) as targeted_count,
              sum(case status when 'init' then 1 else 0 end) as init_count,
              sum(case status when 'forward' then 1 else 0 end) as forward_count,
              sum(case status when 'complete' then 1 else 0 end) as complete_count,
              sum(case status when 'screenout' then 1 else 0 end) as screenout_count,
              sum(case status when 'quotafull' then 1 else 0 end) as quotafull_count,
              sum(case status when 'error' then 1 else 0 end) as error_count,
              sum(case when status in ('complete', 'screenout', 'quotafull', 'error') then 1 else 0 end) as csqe_count
              from survey_sop_participation_history
              where date_sub(curdate(), interval 30 day) <= date(created_at)
              group by date(created_at)
              order by date(created_at) desc
            ) as t
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();

        return $this->render('WenwenFrontendBundle:admin:adminReportSopDailyReport.html.twig', array('result' => $result));
    }

    /**
     * @Route("/sop/detail_report", name="admin_report_sop_detail_report")
     */
    public function adminReportSopDetailReport(Request $request)
    {
        $sql = "
            select a.*, b.*
            from survey_sop a
            left join (
              select *,
              round(complete_count / csqe_count * 100, 2) as real_ir,
              round(forward_count / init_count * 100, 2) as cvr1,
              round(csqe_count / forward_count * 100, 2) as cvr2,
              round(csqe_count / targeted_count * 100, 2) as cvr3,
              round(sum_complete_loi / sum_complete_count / 60) as avg_complete_loi,
              round(sum_screenout_loi / sum_screenout_count / 60) as avg_screenout_loi
              from (
                select survey_id,
                sum(case status when 'targeted' then 1 else 0 end) as targeted_count,
                sum(case status when 'init' then 1 else 0 end) as init_count,
                sum(case status when 'forward' then 1 else 0 end) as forward_count,
                sum(case status when 'complete' then 1 else 0 end) as complete_count,
                sum(case status when 'screenout' then 1 else 0 end) as screenout_count,
                sum(case status when 'quotafull' then 1 else 0 end) as quotafull_count,
                sum(case status when 'error' then 1 else 0 end) as error_count,
                sum(case when status in ('complete', 'screenout', 'quotafull', 'error') then 1 else 0 end) as csqe_count,
                sum(case when status='complete' and client_ip is not null then loi else 0 end) as sum_complete_loi,
                sum(case when status='complete' and client_ip is not null then 1 else 0 end) as sum_complete_count,
                sum(case when status in ('screenout', 'quotafull', 'error') and client_ip is not null then loi else 0 end) as sum_screenout_loi,
                sum(case when status in ('screenout', 'quotafull', 'error') and client_ip is not null then 1 else 0 end) as sum_screenout_count
                from survey_sop_participation_history
                group by survey_id
              ) sb
            ) b
            on a.survey_id = b.survey_id
            group by b.survey_id
            order by a.is_closed asc, a.id desc
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();
        $pagination = $this->get('knp_paginator')->paginate($result, $request->query->getInt('page', 1), 100);

        return $this->render('WenwenFrontendBundle:admin:adminReportSopDetailReport.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/sop/daily_report_2", name="admin_report_sop_daily_report_2")
     */
    public function adminReportSopDailyReport2(Request $request)
    {
        $today = date('Y-m-d');
        $addedCount = 0;
        $closedCount = 0;

        $em = $this->getDoctrine()->getManager();
        $surveys = $em
            ->createQuery("select t from WenwenFrontendBundle:SurveySop t where date(t.createdAt) = ?1")
            ->setParameter(1, $today)
            ->getResult();

        foreach ($surveys as $survey) {
            $addedCount++;
            if ($survey->getIsClosed() === true) {
                $closedCount++;
            }
        }

        $availableCount = $em
            ->createQuery("select count(t.id) from WenwenFrontendBundle:SurveySop t where date(t.createdAt) <= ?1 and t.isClosed = 0")
            ->setParameter(1, $today)
            ->getSingleScalarResult();

        $newLine = array(
            'created_date' => $today,
            'added_count' => $addedCount,
            'closed_count' => $closedCount,
            'available_count' => $availableCount,
        );

        $lines = $em
            ->getConnection()
            ->executeQuery("select * from survey_sop_daily_report order by created_date desc")
            ->fetchAll();

        array_unshift($lines, $newLine);

//        print_r($lines);

        return $this->render('WenwenFrontendBundle:admin:adminReportSopDailyReport2.html.twig', array('result' => $lines));
    }
}
