<?php
namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Jili\BackendBundle\Form\PanelistSearchType;
use Jili\BackendBundle\Form\PanelistEditFormType;
use \VendorIntegration\SSI\PC1\Constants;

/**
 * @Route("/admin/panelist",requirements={"_scheme"="https"})
 */
class AdminPanelistController extends Controller implements IpAuthenticatedController
{

    /**
     * @Route("/search",  name="_admin_panelist_index")
     */
    public function searchAction(Request $request)
    {
        // page size
        $pageSize = $this->container->getParameter('page_size_50');

        //get page
        $page = (int) $request->request->get('page', 1);
        if (!$page) {
            $page = (int) $request->query->get('page', 1);
        }

        if ($page < 1) {
            $page = 1;
        }

        // create form
        $form = $this->createForm(new PanelistSearchType());

        // default value
        $registeredCount = 0;
        $withdrawalCount = 0;

        $em = $this->getDoctrine()->getManager();

        // commit search
        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid()) {

                $values = $form->getData();

                // get registered user list
                if ($values['type_registered'] == 1) {
                    $registeredCount = $em->getRepository('JiliApiBundle:User')->getSearchUserCount($values, 'registered');
                    $registered_page = $page > (int) ceil($registeredCount / $pageSize) ? (int) ceil($registeredCount / $pageSize) : $page;
                    $arr['registeredUserList'] = $em->getRepository('JiliApiBundle:User')->getSearchUserList($values, 'registered', $pageSize, $registered_page);

                    foreach ($arr['registeredUserList'] as $key => $value) {
                        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($value['id']);
                        $arr['registeredUserList'][$key]['ssi_respondent_exist'] = $ssi_respondent ? true : false;
                    }
                }

                // get withdrawal user list
                if ($values['type_withdrawal'] == 1) {
                    $withdrawalCount = $em->getRepository('JiliApiBundle:User')->getSearchUserCount($values, 'withdrawal');
                    $withdrawal_page = $page > (int) ceil($withdrawalCount / $pageSize) ? (int) ceil($withdrawalCount / $pageSize) : $page;
                    $arr['withdrawalUserList'] = $em->getRepository('JiliApiBundle:User')->getSearchUserList($values, 'withdrawal', $pageSize, $withdrawal_page);
                }
            }
        }

        // page choose
        if ($registeredCount > $withdrawalCount) {
            $arr['total'] = $registeredCount;
        } else {
            $arr['total'] = $withdrawalCount;
        }

        $arr['page'] = $page;
        $arr['page_size'] = $pageSize;
        $arr['form'] = $form->createView();
        $arr['registeredCount'] = $registeredCount;
        $arr['withdrawalCount'] = $withdrawalCount;

        //get sop config
        $arr['sop'] = $this->container->getParameter('sop');

        return $this->render('JiliBackendBundle:Panelist:search.html.twig', $arr);
    }

    /**
     * @Route("/edit", name="_admin_panelist_edit")
     */
    public function editAction(Request $request)
    {
        $user_id = $request->query->get('id', '');
        $completed = $request->query->get('completed');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        // user not exist
        if (!$user) {
            $arr['user'] = null;

            return $this->render('JiliBackendBundle:Panelist:edit.html.twig', $arr);
        }

        // set delete flag
        if (is_null($user->getDeleteFlag())) {
            $user->setDeleteFlag(0);
        }

        // get user hobby name
        $arr['user_hobby_name'] = $em->getRepository('JiliApiBundle:HobbyList')->getUserHobbyName($user->getHobby());

        // create form
        $form = $this->createForm(new PanelistEditFormType(), $user);
        $arr['form'] = $form->createView();

        $arr['user'] = $user;
        $arr['completed'] = $completed;

        return $this->render('JiliBackendBundle:Panelist:edit.html.twig', $arr);
    }

    /**
     * @Route("/editConfirm", name="_admin_panelist_edit_confirm")
     * @Method("POST")
     */
    public function editConfirmAction(Request $request)
    {
        //create vote form and get form data
        $form = $this->createForm(new PanelistEditFormType());
        $form->bind($request);

        $values = $form->getData();

        # If PID don't match, reset things and return to search
        if (!$values || !isset($values['id'])) {
            return $this->redirect($this->generateUrl('_admin_panelist_index'));
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($values['id']);

        // user not exist
        if (!$user) {
            return $this->redirect($this->generateUrl('_admin_panelist_index'));
        }

        # Undo is clicked
        if ($request->request->get('undo')) {
            return $this->redirect($this->generateUrl('_admin_panelist_edit', array (
                'id' => $values['id']
            )));
        }

        if ($form->isValid()) {
            return $this->render('JiliBackendBundle:Panelist:editConfirm.html.twig', array (
                'form' => $form->createView(),
                'user' => $user,
                'values' => $values,
                'user_hobby_name' => $em->getRepository('JiliApiBundle:HobbyList')->getUserHobbyName($user->getHobby())
            ));
        }

        //form invalid
        $error_messages = $form->getErrors();

        return $this->render('JiliBackendBundle:Panelist:edit.html.twig', array (
            'form' => $form->createView(),
            'error_messages' => $error_messages,
            'user' => $user,
            'user_hobby_name' => $em->getRepository('JiliApiBundle:HobbyList')->getUserHobbyName($user->getHobby())
        ));
    }

    /**
     * @Route("/editCommit", name="_admin_panelist_edit_commit")
     * @Method("POST")
     */
    public function editCommitAction(Request $request)
    {
        $form = $this->createForm(new PanelistEditFormType());
        $form->bind($request);

        $values = $form->getData();

        # If PID don't match, reset things and return to search
        if (!$values || !isset($values['id'])) {
            return $this->redirect($this->generateUrl('_admin_panelist_index'));
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($values['id']);

        // user not exist
        if (!$user) {
            return $this->redirect($this->generateUrl('_admin_panelist_index'));
        }

        # Undo is clicked
        if ($request->request->get('undo')) {
            return $this->redirect($this->generateUrl('_admin_panelist_edit', array (
                'id' => $values['id']
            )));
        }

        if ($form->isValid()) {

            // update user info
            $user->setBirthday($values['birthday']);
            $user->setNick($values['nick']);
            $user->setTel($values['tel']);
            $user->setDeleteFlag($values['deleteFlag']);
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('_admin_panelist_edit', array (
                'id' => $values['id'],
                'completed' => 1
            )));
        }

        //form invalid
        return $this->redirect($this->generateUrl('_admin_panelist_edit', array (
            'id' => $values['id']
        )));
    }

    /**
     * @Route("/pointHistory",  name="_admin_panelist_pointhistory")
     */
    public function pointHistoryAction(Request $request)
    {
        $user_id = $request->query->get('id', '');
        $pageSize = $this->container->getParameter('page_size_50');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        // user not exist
        if (!$user) {
            $arr['user'] = null;

            return $this->render('JiliBackendBundle:Panelist:pointhistory.html.twig', $arr);
        }

        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        // user point history total count
        $pointHistoryCount = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->userPointHistoryCount($user_id);

        //user point history list
        $pointHistoryList = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->userPointHistorySearch($user_id, $pageSize, $page);

        //user total_point by point history
        foreach ($pointHistoryList as $key => $value) {
            $pointHistoryList[$key]['total_point'] = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->userTotalPoint($user_id, $value['id']);
        }

        $arr['page'] = $page;
        $arr['page_size'] = $pageSize;
        $arr['total'] = $pointHistoryCount;
        $arr['user'] = $user;
        $arr['pointHistoryList'] = $pointHistoryList;

        return $this->render('JiliBackendBundle:Panelist:pointhistory.html.twig', $arr);
    }

    /**
     * @Route("/ssiRespondentSummary",  name="_admin_panelist_ssirespondentsummary")
     */
    public function ssiRespondentSummaryAction(Request $request)
    {
        $user_id = $request->query->get('id');
        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $pageSize = $this->container->getParameter('page_size_50');
        $em = $this->getDoctrine()->getManager();

        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user_id);
        if (!$ssi_respondent) {
            $arr['ssi_respondent'] = null;

            return $this->render('JiliBackendBundle:Panelist:ssiRespondentSummary.html.twig', $arr);
        }

        if ($ssi_respondent->isActive()) {
            $ssi_respondent_status = 'ACTIVE';
        } elseif ($ssi_respondent->needPrescreening()) {
            $ssi_respondent_status = 'PRE-SCREENING';
        } else {
            $ssi_respondent_status = 'INACTIVE';
        }

        try {

            //total
            $ssi_survey_total = $em->getRepository('WenwenAppBundle:SsiProjectRespondent')->retrieveAllForRespondentCount($ssi_respondent->getId());

            //list
            $ssi_surveys = $em->getRepository('WenwenAppBundle:SsiProjectRespondent')->retrieveAllForRespondent($ssi_respondent->getId(), $pageSize, $page);
            // echo "<br>line_".__LINE__."_aaaaaaaaaa<pre>";
            // print_r($ssi_surveys);
            $ssi_survey_infos = array ();
            foreach ($ssi_surveys as $key => $value) {
                //                 $ssi_survey_infos[$key]['answer_status'] = $this->getAnswerStatusInfo($value->getAnswerStatus());
                $ssi_surveys[$key]->setAnswerStatus($this->getAnswerStatusInfo($value->getAnswerStatus()));
            }
        } catch (Exception $e) {

            $logger = $this->get('logger');
            $logger->info($e);

            $ssi_surveys = array ();
        }

        $arr['page'] = $page;
        $arr['page_size'] = $pageSize;
        $arr['total'] = $ssi_survey_total;
        $arr['ssi_respondent_status'] = $ssi_respondent_status;
        $arr['ssi_respondent'] = $ssi_respondent;
        $arr['ssi_surveys'] = $ssi_surveys;

        return $this->render('JiliBackendBundle:Panelist:ssiRespondentSummary.html.twig', $arr);
    }

    public function getAnswerStatusInfo($answer_status)
    {
        $answer_status_string = '';
        switch ($answer_status) {
            case Constants::SSI_PROJECT_RESPONDENT_STATUS_INIT :
                $answer_status_string = 'INIT';
                break;
            case Constants::SSI_PROJECT_RESPONDENT_STATUS_REOPENED :
                $answer_status_string = 'RE-OPENED';
                break;
            case Constants::SSI_PROJECT_RESPONDENT_STATUS_FORWARDED :
                $answer_status_string = 'FORWARDED';
                break;
            case Constants::SSI_PROJECT_RESPONDENT_STATUS_COMPLETE :
                $answer_status_string = 'DONE';
                break;
            default :
                $answer_status_string = 'Unknown status';
                break;
        }

        return $answer_status_string;
    }
}
