<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wenwen\FrontendBundle\Form\SsiPartnerPermissionType;
use Wenwen\AppBundle\Entity\SsiRespondent;
use Wenwen\FrontendBundle\Entity\CategoryType;
use Wenwen\FrontendBundle\Entity\TaskType;

/**
 * @Route("/ssi_partner")
 */
class SsiPartnerController extends Controller
{

    /**
     * @Route("/permission", name="_ssi_partner_permission")
     * @Template
     */
    public function permissionAction(Request $request)
    {
        // ログインしていない
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('errors', array (
                'panelist_is_not_authenticated' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        $em = $this->getDoctrine()->getManager();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($request->getSession()->get('uid'));

        //go to permission page
        if (!$ssi_respondent) {
            $form = $this->createForm(new SsiPartnerPermissionType());
            return $this->render('WenwenFrontendBundle:SsiPartner:permission.html.twig', array (
                'form' => $form->createView()
            ));
        }

        // 同意未回答
        if ($ssi_respondent->needPrescreening()) {
            // 属性アンケート未回答の人 -> ページヘ遷移
            return $this->redirect($this->generateUrl('_ssi_partner_prescreen'));
        }

        // 回答済み
        $request->getSession()->set('errors', array (
            'panelist_has_already_answered' => true
        ));

        return $this->redirect($this->generateUrl('_ssi_partner_error'));
    }

    /**
     * @Route("/commit", name="_ssi_partner_commit")
     * @Template
     */
    public function commitAction(Request $request)
    {
        // ログインしていない
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('errors', array (
                'panelist_is_not_authenticated' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        $em = $this->getDoctrine()->getManager();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($request->getSession()->get('uid'));

        if ($ssi_respondent) {
            // 同意未回答
            if ($ssi_respondent->needPrescreening()) {
                // 属性アンケート未回答の人 -> ページヘ遷移
                return $this->redirect($this->generateUrl('_ssi_partner_prescreen'));
            }

            // 回答済み
            $request->getSession()->set('errors', array (
                'panelist_has_already_answered' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        $form = $this->createForm(new SsiPartnerPermissionType());
        $form->bind($request);

        $values = $form->getData();

        if ($form->isValid()) {
            $user = $em->getRepository('WenwenFrontendBundle:User')->findOneById($request->getSession()->get('uid'));

            $ssi_respondent = new SsiRespondent();
            $ssi_respondent->setUser($user);
            $ssi_respondent->setStatusFlag($values['permission_flag']);
            $em->persist($ssi_respondent);
            $em->flush();

            // permission: yes
            if ($ssi_respondent->needPrescreening()) {
                return $this->redirect($this->generateUrl('_ssi_partner_redirect'));
            } else {
                // permission no : add point
                $point_value = 1;
                $this->get('points_manager')->updatePoints($user->getId(), 
                    $point_value, 
                    CategoryType::SSI_EXPENSE, 
                    TaskType::RENTENTION, 
                    '同意参与海外市场调查项目'
                    );

                return $this->redirect($this->generateUrl('_ssi_partner_complete'));
            }
        }

        return $this->render('WenwenFrontendBundle:SsiPartner:permission.html.twig', array (
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/complete", name="_ssi_partner_complete")
     * @Template
     */
    public function completeAction(Request $request)
    {
        // ログインしていない
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('errors', array (
                'panelist_is_not_authenticated' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        return $this->render('WenwenFrontendBundle:SsiPartner:complete.html.twig');
    }

    /**
     * @Route("/prescreen", name="_ssi_partner_prescreen")
     * @Template
     */
    public function prescreenAction(Request $request)
    {
        // ログインしていない
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('errors', array (
                'panelist_is_not_authenticated' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        $em = $this->getDoctrine()->getManager();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($request->getSession()->get('uid'));

        // 資格なし
        if (!$ssi_respondent || !$ssi_respondent->needPrescreening()) {
            $request->getSession()->set('errors', array (
                'panelist_has_already_answered' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        return $this->render('WenwenFrontendBundle:SsiPartner:prescreen.html.twig');
    }

    /**
     * @Route("/prescreeningComplete", name="_ssi_partner_prescreeningcomplete")
     * @Template
     */
    public function prescreeningCompleteAction(Request $request)
    {
        // ログインしていない
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('errors', array (
                'panelist_is_not_authenticated' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        $em = $this->getDoctrine()->getManager();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($request->getSession()->get('uid'));

        // 資格なし
        if (!$ssi_respondent || !$ssi_respondent->needPrescreening()) {
            $request->getSession()->set('errors', array (
                'panelist_has_already_answered' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        // 念入り
        if ($ssi_respondent->needPrescreening()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $db_connection = $em->getConnection();
                $db_connection->beginTransaction();

                // ステータスを書き換えてポイント付与
                $ssi_respondent->setStatusFlag(SsiRespondent::STATUS_PRESCREENED);
                $em->persist($ssi_respondent);
                $em->flush();

                // add point
                $point_value = 1;
                $user_id = $request->getSession()->get('uid');
                $this->get('points_manager')->updatePoints($user_id, 
                    $point_value, 
                    CategoryType::SSI_EXPENSE, 
                    TaskType::RENTENTION, 
                    '完成海外市场调查项目Prescreen'
                    );

                $db_connection->commit();
            } catch (\Exception $e) {
                $db_connection->rollback();
                $this->get('logger')->critical('ssi partner prescreeningcomplete fail' . $e->getMessage());
            }
        }

        return $this->render('WenwenFrontendBundle:SsiPartner:complete.html.twig');
    }

    /**
     * @Route("/redirect", name="_ssi_partner_redirect")
     * @Template
     */
    public function redirectAction(Request $request)
    {
        // ログインしていない
        if (!$request->getSession()->get('uid')) {
            $request->getSession()->set('errors', array (
                'panelist_is_not_authenticated' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        $em = $this->getDoctrine()->getManager();
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($request->getSession()->get('uid'));

        // 資格なし
        if (!$ssi_respondent || !$ssi_respondent->needPrescreening()) {
            $request->getSession()->set('errors', array (
                'panelist_has_already_answered' => true
            ));
            return $this->redirect($this->generateUrl('_ssi_partner_error'));
        }

        return $this->redirect($ssi_respondent->getPrescreeningSurveyUrl());
    }

    /**
     * @Route("/error", name="_ssi_partner_error")
     * @Template
     */
    public function errorAction(Request $request)
    {
        $response = new Response();
        $response->setStatusCode(403);

        $errors = $request->getSession()->get('errors');

        return $this->render('WenwenFrontendBundle:SsiPartner:error.html.twig', array (
            'errors' => $errors
        ), $response);
    }

}
