<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wenwen\FrontendBundle\Entity\SurveyGmoNonBusiness;
use Wenwen\FrontendBundle\Form\SurveyGmoNonBusinessType;

/**
 * SurveyGmoNonBusiness controller.
 *
 * @Route("/admin/gmo/report")
 */
class AdminSurveyGmoReportController extends Controller
{
    /**
     * Lists all SurveyGmo entities.
     *
     * @Route("/surveylist", name="admin_gmo_report_surveylist")
     * @Method("GET")
     */
    public function surveylistAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('WenwenFrontendBundle:SurveyGmo')->findBy(array(), array('createdAt' => 'DESC'));

        return $this->render("WenwenFrontendBundle:admin:SurveyGmoReport/surveylist.html.twig", array(
            'entities' => $entities,
        ));
    }

    /**
     * Show monthly participation report.
     *
     * @Route("/monthly_participation", name="admin_gmo_report_monthly_participation")
     * @Method("GET")
     */
    public function monthlyParticipationAction()
    {
        $sql = "
            select *,
            round(forward_count / init_count * 100, 2) as init_to_forward,
            round(csqe_count / forward_count * 100, 2) as forward_to_csqe,
            round(csqe_count / targeted_count * 100, 2) as targeted_to_csqe,
            round(forward_count / targeted_count * 100, 2) as targeted_to_forward,
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
              from survey_gmo_participation_history
              where date_sub(curdate(), interval 800 day) <= date(created_at)
              group by created_month
            ) as t
            join (
              select date_format(created_at, '%Y-%m') as created_month, count(id) as new_project_cnt 
              from survey_gmo group by created_month
            ) as p on (t.created_month = p.created_month)
            order by t.created_month desc
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAll();

        return $this->render('WenwenFrontendBundle:admin:SurveyGmoReport/monthlyParticipationReport.html.twig', array('result' => $result));
    }

}
