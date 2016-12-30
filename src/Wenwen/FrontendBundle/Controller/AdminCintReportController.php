<?php

namespace Wenwen\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\BackendBundle\Controller\IpAuthenticatedController;

/**
 * @Route("/admin/report")
 */
class AdminCintReportController extends BaseController #implements IpAuthenticatedController
{
    /**
     * @Route("/cint/daily_report", name="admin_report_cint_daily_report")
     */
    public function adminReportCintDailyReport(Request $request)
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
              from survey_cint_participation_history
              where date_sub(curdate(), interval 30 day) <= date(created_at)
              group by date(created_at)
              order by date(created_at) desc
            ) as t
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();

        return $this->render('WenwenFrontendBundle:admin:adminReportCintDailyReport.html.twig', array('result' => $result));
    }

    /**
     * @Route("/cint/detail_report", name="admin_report_cint_detail_report")
     */
    public function adminReportCintDetailReport(Request $request)
    {
        $sql = "
            select a.*, b.*
            from survey_cint a
            left join (
              select *,
              round(complete_count / csqe_count * 100, 2) as real_ir,
              round(forward_count / init_count * 100, 2) as cvr1,
              round(csqe_count / forward_count * 100, 2) as cvr2,
              round(csqe_count / targeted_count * 100, 2) as cvr3
              from (
                select survey_id,
                sum(case status when 'targeted' then 1 else 0 end) as targeted_count,
                sum(case status when 'init' then 1 else 0 end) as init_count,
                sum(case status when 'forward' then 1 else 0 end) as forward_count,
                sum(case status when 'complete' then 1 else 0 end) as complete_count,
                sum(case status when 'screenout' then 1 else 0 end) as screenout_count,
                sum(case status when 'quotafull' then 1 else 0 end) as quotafull_count,
                sum(case status when 'error' then 1 else 0 end) as error_count,
                sum(case when status in ('complete', 'screenout', 'quotafull', 'error') then 1 else 0 end) as csqe_count
                from survey_cint_participation_history
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

        return $this->render('WenwenFrontendBundle:admin:adminReportCintDetailReport.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/cint/daily_report_2", name="admin_report_cint_daily_report_2")
     */
    public function adminReportCintDailyReport2(Request $request)
    {
        $today = date('Y-m-d');
        $addedCount = 0;
        $closedCount = 0;

        $em = $this->getDoctrine()->getManager();
        $surveys = $em
            ->createQuery("select t from WenwenFrontendBundle:SurveyCint t where date(t.createdAt) = ?1")
            ->setParameter(1, $today)
            ->getResult();

        foreach ($surveys as $survey) {
            $addedCount++;
            if ($survey->getIsClosed() === true) {
                $closedCount++;
            }
        }

        $availableCount = $em
            ->createQuery("select count(t.id) from WenwenFrontendBundle:SurveyCint t where date(t.createdAt) <= ?1 and t.isClosed = 0")
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
            ->executeQuery("select * from survey_cint_daily_report order by created_date desc")
            ->fetchAll();

        array_unshift($lines, $newLine);

//        print_r($lines);

        return $this->render('WenwenFrontendBundle:admin:adminReportCintDailyReport2.html.twig', array('result' => $lines));
    }
}
