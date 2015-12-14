<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\HttpKernel\HttpKernelInterface;
// use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Jili\BackendBundle\Form\PanelistSearchType;
use Jili\ApiBundle\Utility\String;
use Jili\BackendBundle\Form\PanelistEditFormType;
// use jili\ApiBundle\Entity\User;


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
        $pageSize = $this->container->getParameter('page_size_50');

        $page = (int) $request->request->get('page', 1);
        if (!$page) {
            $page = (int) $request->query->get('page', 1);
        }

        if ($page < 1) {
            $page = 1;
        }

        //create vote form
        $form = $this->createForm(new PanelistSearchType());
        $pagination = null;
        $registeredCount = 0;
        $withdrawalCount = 0;
        $pageCount = 0;
        $pages = 0;

        $em = $this->getDoctrine()->getManager();
        if ($request->getMethod() === 'POST') {
            $form->bind($request);

            if ($form->isValid()) {

                $values = $form->getData();

                if ($values['type_registered'] == 1) {
                    $registeredCount = $em->getRepository('JiliApiBundle:User')->getSearchUserCount($values, 'registered');
                    $registered_page = $page > (int) ceil($registeredCount / $pageSize) ? (int) ceil($registeredCount / $pageSize) : $page;
                    $arr['registeredUserList'] = $em->getRepository('JiliApiBundle:User')->getSearchUserList($values, 'registered', $pageSize, $registered_page);
                }

                if ($values['type_withdrawal'] == 1) {
                    $withdrawalCount = $em->getRepository('JiliApiBundle:User')->getSearchUserCount($values, 'withdrawal');
                    $withdrawal_page = $page > (int) ceil($withdrawalCount / $pageSize) ? (int) ceil($withdrawalCount / $pageSize) : $page;
                    $arr['withdrawalUserList'] = $em->getRepository('JiliApiBundle:User')->getSearchUserList($values, 'withdrawal', $pageSize, $withdrawal_page);
                }
            }
        }

        //todo: 了解一下enqueteHistory, PartnerPublicationPanelistManager


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
        $arr['sop'] = $this->container->getParameter('sop');
        return $this->render('JiliBackendBundle:Panelist:search.html.twig', $arr);
    }

    /**
     * @Route("/edit", name="_admin_panelist_edit")
     */
    public function editAction(Request $request)
    {
        $user_id = $request->query->get('id');
        $completed = $request->query->get('completed');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        if (!$user) {
            $arr['user'] = null;
            return $this->render('JiliBackendBundle:Panelist:edit.html.twig', $arr);
        }

        if (is_null($user->getDeleteFlag())) {
            $user->setDeleteFlag(0);
        }

        $form = $this->createForm(new PanelistEditFormType(), $user);

        $arr['form'] = $form->createView();

        $arr['user'] = $user;
        $arr['user_hobby_name'] = $this->getUserHobbyName($user->getHobby());
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
                'user_hobby_name' => $this->getUserHobbyName($user->getHobby())
            ));
        }

        //form invalid
        $error_meeeages = $form->getErrors();
        return $this->render('JiliBackendBundle:Panelist:edit.html.twig', array (
            'form' => $form->createView(),
            'error_meeeages' => $error_meeeages,
            'user' => $user,
            'user_hobby_name' => $this->getUserHobbyName($user->getHobby())
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
        $user_id = $request->query->get('id');
        $pageSize = $this->container->getParameter('page_size_50');

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);

        if (!$user) {
            $arr['user'] = null;
            return $this->render('JiliBackendBundle:Panelist:pointhistory.html.twig', $arr);
        }

        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        // total count
        $pointHistoryCount = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->userPointHistoryCount($user_id);

        //list
        $pointHistoryList = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->userPointHistorySearch($user_id, $pageSize, $page);

        //total_point
        $total_point = 0;
        foreach ($pointHistoryList as $key => $value) {
            $pointHistoryList[$key]['total_point'] = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->userTotalPoint($value['id']);
        }

        $arr['page'] = $page;
        $arr['page_size'] = $pageSize;
        $arr['total'] = $pointHistoryCount;
        $arr['user'] = $user;
        $arr['pointHistoryList'] = $pointHistoryList;
        return $this->render('JiliBackendBundle:Panelist:pointhistory.html.twig', $arr);
    }

    public function getUserHobbyName($user_hobby)
    {
        $em = $this->getDoctrine()->getManager();
        $user_hobby_name = '';

        $user_hobby_arr = explode(",", $user_hobby);
        foreach ($user_hobby_arr as $key => $value) {
            $hobby = $em->getRepository('JiliApiBundle:HobbyList')->find($value);
            $user_hobby_names[] = $hobby->getHobbyName();
        }
        $user_hobby_name = implode(',', $user_hobby_names);
        return $user_hobby_name;
    }
}
