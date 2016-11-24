<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Jili\BackendBundle\Controller\IpAuthenticatedController;

class AdminRecruitController extends BaseController implements IpAuthenticatedController
{
    /**
     * @Route("/admin/recruit/report/daily/", name="admin_recruit_report_daily")
     * @Template
     */
    public function showDailyReportAction(Request $request){

        $adminRecruitService = $this->get('app.admin_recruit_service');
        $return = $adminRecruitService->getDailyReport();
        return $this->render('WenwenFrontendBundle:admin:recruitReportDaily.html.twig', 
            array(
                'return' => $return
                ));
    }

    /**
     * @Route("/admin/recruit/report/monthly/", name="admin_recruit_report_monthly")
     * @Template
     */
    public function showMonthlyReportAction(Request $request){

        $adminRecruitService = $this->get('app.admin_recruit_service');
        $return = $adminRecruitService->getMonthlyReport();
        return $this->render('WenwenFrontendBundle:admin:recruitReportMonthly.html.twig', 
            array(
                'return' => $return
                ));
    }


}
