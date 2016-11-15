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
     * @Route("/admin/recruit/report/", name="admin_recruit_report")
     * @Template
     */
    public function showReportAction(Request $request){

        $adminRecruitService = $this->get('app.admin_recruit_service');
        $return = $adminRecruitService->getReport();
        return $this->render('WenwenFrontendBundle:admin:recruitReport.html.twig', 
            array(
                'return' => $return
                ));
    }


}
