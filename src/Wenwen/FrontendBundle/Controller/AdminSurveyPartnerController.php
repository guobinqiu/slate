<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wenwen\FrontendBundle\Entity\SurveyPartner;
use Wenwen\FrontendBundle\Form\SurveyPartnerType;
use Jili\BackendBundle\Controller\IpAuthenticatedController;

class AdminSurveyPartnerController extends BaseController implements IpAuthenticatedController
{
    /**
     * @Route("/admin/surveypartner/list/", name="admin_surveypartner_list")
     * @Template
     */
    public function showSurveyPartnerListAction(Request $request){
        $currentPage = $request->query->get('page', 1);
        $adminSurveyPartnerService = $this->get('app.admin_survey_partner_service');
        $pagination = $adminSurveyPartnerService->getSurveyPartnerList($currentPage, 50);
        return $this->render('WenwenFrontendBundle:admin:surveyPartnerList.html.twig', 
            array(
                'pagination' => $pagination
                ));
    }

    /**
     * @Route("/admin/surveypartner/daily_report/", name="admin_surveypartner_daily_report")
     * @Template
     */
    public function showParticipationDailyReportAction(Request $request){
        // 
        $adminSurveyPartnerService = $this->get('app.admin_survey_partner_service');

        // 先直接显示过去一周的参与情况情况
        $result = $adminSurveyPartnerService->getParticipationDailyReport();

        return $this->render('WenwenFrontendBundle:admin:surveyPartnerDailyReport.html.twig', 
            array(
                'dailyReports' => $result['dailyReports']
                ));

    }

    /**
     * @Route("/admin/surveypartner/open/{surveyPartnerId}/", name="admin_surveypartner_open")
     * @Template
     */
    public function openSurveyPartnerAction(Request $request, $surveyPartnerId)
    {
        $this->get('logger')->debug(__METHOD__ . ' START surveyPartnerId=' . $surveyPartnerId);

        $adminSurveyPartnerService = $this->get('app.admin_survey_partner_service');
        $adminSurveyPartnerService->openSurveyPartner($surveyPartnerId);
        return $this->redirect($this->generateUrl('admin_surveypartner_show', array('surveyPartnerId' => $surveyPartnerId)));
    }

    /**
     * @Route("/admin/surveypartner/close/{surveyPartnerId}/", name="admin_surveypartner_close")
     * @Template
     */
    public function closeSurveyPartnerAction(Request $request, $surveyPartnerId)
    {
        $this->get('logger')->debug(__METHOD__ . ' START surveyPartnerId=' . $surveyPartnerId);

        $adminSurveyPartnerService = $this->get('app.admin_survey_partner_service');
        $adminSurveyPartnerService->closeSurveyPartner($surveyPartnerId);
        return $this->redirect($this->generateUrl('admin_surveypartner_show', array('surveyPartnerId' => $surveyPartnerId)));
    }

    /**
     * @Route("/admin/surveypartner/show/{surveyPartnerId}/", name="admin_surveypartner_show")
     * @Template
     */
    public function showSurveyPartnerAction(Request $request, $surveyPartnerId)
    {
        $this->get('logger')->debug(__METHOD__ . ' START surveyPartnerId=' . $surveyPartnerId);

        $adminSurveyPartnerService = $this->get('app.admin_survey_partner_service');
        $surveyPartner = $adminSurveyPartnerService->findSurveyPartner($surveyPartnerId);

        if(! is_null($surveyPartner)){
            $currentPage = $request->query->get('page', 1);
            $pagination = $adminSurveyPartnerService->getSurveyPartnerParticipationDetail($surveyPartner, $currentPage);
            $summary = $adminSurveyPartnerService->getSurveyPartnerParticipationSummary($surveyPartner);
        }

        $form = $this->createForm(new SurveyPartnerType(), $surveyPartner, array('disabled' => true));
        
        $this->get('logger')->debug(__METHOD__ . ' END surveyPartnerId=' . $surveyPartnerId);
        return $this->render('WenwenFrontendBundle:admin:surveyPartnerShow.html.twig', 
            array(
                'surveyPartnerForm' => $form->createView(),
                'surveyPartnerId' => $surveyPartner->getId(),
                'surveyPartnerStatus' => $surveyPartner->getStatus(),
                'summary' => $summary,
                'pagination' => $pagination,
                ));
    }

	/**
     * @Route("/admin/surveypartner/create/", name="admin_surveypartner_create")
     * @Template
     */
    public function createSurveyPartnerAction(Request $request)
    {
        $this->get('logger')->debug(__METHOD__ . ' START');
        $surveyPartner = new SurveyPartner();
        $surveyPartner->setReentry(false);
        $surveyPartner->setCompletePoint(300);
        $surveyPartner->setScreenoutPoint(1);
        $surveyPartner->setQuotafullPoint(2);
        $surveyPartner->setMinAge(0);
        $surveyPartner->setMaxAge(150);
        $surveyPartner->setGender(SurveyPartner::GENDER_BOTH);
        $surveyPartner->setStatus('init');
        $surveyPartner->setCreatedAt(new \Datetime());
        $surveyPartner->setUpdatedAt(new \Datetime());
        $form = $this->createForm(new SurveyPartnerType(), $surveyPartner);
        if ($request->getMethod() == 'POST') {
            $this->get('logger')->debug('XXX2');
            $form->bind($request);
            $this->get('logger')->debug('XXX3');
            if ($form->isValid()) {
                $this->get('logger')->debug('XXX4');
                $adminSurveyPartnerService = $this->get('app.admin_survey_partner_service');
                $adminSurveyPartnerService->createUpdateSurveyPartner($surveyPartner);
                $this->get('logger')->debug(__METHOD__ . ' Created');
                return $this->redirect($this->generateUrl('admin_surveypartner_show', array('surveyPartnerId' => $surveyPartner->getId())));
            } else {
                //var_dump($form->getErrors());
                $this->get('logger')->warn(__METHOD__ . ' ERROR:' . json_encode($form->getErrors()));
            }
        }
        $this->get('logger')->debug(__METHOD__ . ' END');
        return $this->render('WenwenFrontendBundle:admin:surveyPartnerCreate.html.twig', array('surveyPartnerForm' => $form->createView()));
    }

    /**
     * @Route("/admin/surveypartner/edit/{surveyPartnerId}/", name="admin_surveypartner_edit")
     * @Template
     */
    public function editSurveyPartnerAction(Request $request, $surveyPartnerId){
        $this->get('logger')->debug(__METHOD__ . ' surveyPartnerId=' . $surveyPartnerId);

        $adminSurveyPartnerService = $this->get('app.admin_survey_partner_service');
        $surveyPartner = $adminSurveyPartnerService->findSurveyPartner($surveyPartnerId);
        
        $form = $this->createForm(new SurveyPartnerType(), $surveyPartner);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $surveyPartner->setUpdatedAt(new \DateTime());
                $adminSurveyPartnerService->createUpdateSurveyPartner($surveyPartner);
                $this->get('logger')->debug(__METHOD__ . ' Saved surveyPartnerId=' . $surveyPartnerId);
                return $this->redirect($this->generateUrl('admin_surveypartner_show', array('surveyPartnerId' => $surveyPartnerId)));
            }
        }
        $this->get('logger')->debug(__METHOD__ . ' END surveyPartnerId=' . $surveyPartnerId);
        return $this->render('WenwenFrontendBundle:admin:surveyPartnerEdit.html.twig', array('surveyPartnerForm' => $form->createView(), 'surveyPartnerId' => $surveyPartnerId));
    }

}
