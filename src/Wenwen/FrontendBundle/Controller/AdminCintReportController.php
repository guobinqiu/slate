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
            select t1.*,
            round(t1.forward_count / t1.init_count * 100, 2) as cvr1,
            round(t1.csqe_count / t1.forward_count * 100, 2) as cvr2,
            ifnull(t2.additional, 0) as additional,
            count(t3.id) as available
            from (
              select date(created_at) as created_date,
              sum(case status when 'init' then 1 else 0 end) as init_count,
              sum(case status when 'forward' then 1 else 0 end) as forward_count,
              sum(case status when 'complete' then 1 else 0 end) as complete_count,
              sum(case status when 'screenout' then 1 else 0 end) as screenout_count,
              sum(case status when 'quotafull' then 1 else 0 end) as quotafull_count,
              sum(case status when 'error' then 1 else 0 end) as error_count,
              sum(case when status in ('complete', 'screenout', 'quotafull', 'error') then 1 else 0 end) as csqe_count
              from survey_cint_participation_history
              group by date(created_at)
            ) as t1
            left join (
              select count(*) additional,start_date from survey_cint
              where is_closed = 0
              group by start_date
            ) t2
            on t2.start_date = t1.created_date
            left join (
              select * from survey_cint where is_closed = 0
            ) t3
            on t1.created_date >= t3.start_date
            group by t1.created_date
            order by t1.created_date desc
            limit 30
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
              round(forward_count / init_count * 100, 2) as cvr1,
              round(csqe_count / forward_count * 100, 2) as cvr2
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
            order by a.id desc
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();
        $pagination = $this->get('knp_paginator')->paginate($result, $request->query->getInt('page', 1), 100);

        return $this->render('WenwenFrontendBundle:admin:adminReportCintDetailReport.html.twig', array('pagination' => $pagination));
    }
}
