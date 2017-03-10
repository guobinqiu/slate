<?php

namespace Wenwen\FrontendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\BackendBundle\Controller\IpAuthenticatedController;

/**
 * @Route("/admin/report")
 */
class AdminFulcrumReportController extends BaseController #implements IpAuthenticatedController
{
    /**
     * @Route("/fulcrum/daily_report", name="admin_report_fulcrum_daily_report")
     */
    public function adminReportFulcrumDailyReport(Request $request)
    {
        $sql = "
            select *,
            round(complete_count / (complete_count + screenout_count) * 100, 2) as real_ir,
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
              from survey_fulcrum_participation_history
              where date_sub(curdate(), interval 30 day) <= date(created_at)
              group by date(created_at)
              order by date(created_at) desc
            ) as t
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();

        return $this->render('WenwenFrontendBundle:admin:adminReportFulcrumDailyReport.html.twig', array('result' => $result));
    }

    /**
     * @Route("/fulcrum/detail_report", name="admin_report_fulcrum_detail_report")
     */
    public function adminReportFulcrumDetailReport(Request $request)
    {
        $sql = "
            select a.*, b.*
            from survey_fulcrum a
            left join (
              select *,
              round(complete_count / (complete_count + screenout_count) * 100, 2) as real_ir,
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
                from survey_fulcrum_participation_history
                group by survey_id
              ) sb
            ) b
            on a.survey_id = b.survey_id
            group by b.survey_id
            order by a.id desc
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();
        $pagination = $this->get('knp_paginator')->paginate($result, $request->query->getInt('page', 1), 100);

        return $this->render('WenwenFrontendBundle:admin:adminReportFulcrumDetailReport.html.twig', array('pagination' => $pagination));
    }

    /**
     * @Route("/fulcrum/daily_report_2", name="admin_report_fulcrum_daily_report_2")
     */
    public function adminReportFulcrumDailyReport2(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $lines = [];
        for ($i = 0; $i < 30; $i++) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $addedCount = $em
                ->createQuery("select count(t.id) from WenwenFrontendBundle:SurveyFulcrum t where date(t.createdAt) = ?1")
                ->setParameter(1, $date)
                ->getSingleScalarResult();
            $lines[$i] = array('created_date' => $date, 'added_count' => $addedCount);
        }
        return $this->render('WenwenFrontendBundle:admin:adminReportFulcrumDailyReport2.html.twig', array('result' => $lines));
    }

    /**
     * @Route("/fulcrum/monthly_report", name="admin_report_fulcrum_monthly_report")
     */
    public function adminReportFulcrumMonthlyReport(Request $request)
    {
        $sql = "
            select *,
            round(forward_count / init_count * 100, 2) as cvr1,
            round(csqe_count / forward_count * 100, 2) as cvr2,
            round(csqe_count / targeted_count * 100, 2) as cvr3,
            round(forward_count / targeted_count * 100, 2) as cvr4,
            round(targeted_count / new_project_cnt, 0) as targeted_per_project
            from (
              select date_format(created_at, '%Y-%m') as created_month,
              sum(case status when 'targeted' then 1 else 0 end) as targeted_count,
              sum(case status when 'init' then 1 else 0 end) as init_count,
              sum(case status when 'forward' then 1 else 0 end) as forward_count,
              sum(case status when 'complete' then 1 else 0 end) as complete_count,
              sum(case status when 'screenout' then 1 else 0 end) as screenout_count,
              sum(case status when 'quotafull' then 1 else 0 end) as quotafull_count,
              sum(case status when 'error' then 1 else 0 end) as error_count,
              sum(case when status in ('complete', 'screenout', 'quotafull', 'error') then 1 else 0 end) as csqe_count
              from survey_fulcrum_participation_history
              where date_sub(curdate(), interval 365 day) <= date(created_at)
              group by created_month
            ) as t
            join (
              select date_format(created_at, '%Y-%m') as created_month, count(id) as new_project_cnt 
              from survey_fulcrum group by created_month
            ) as p on (t.created_month = p.created_month)
            order by t.created_month desc
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();

        return $this->render('WenwenFrontendBundle:admin:adminReportFulcrumMonthlyReport.html.twig', array('result' => $result));
    }
}