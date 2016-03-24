<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wenwen\FrontendBundle\Form\SsiPartnerPermissionType;
use Wenwen\AppBundle\Entity\SsiRespondent;
use Jili\ApiBundle\Entity\AdCategory;
use Jili\ApiBundle\Entity\TaskHistory00;

/**
 * @Route("/ssi_partner",requirements={"_scheme"="http"})
 */
class SsiPartnerController extends Controller
{
    private $ssi_respondent;

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

        //permission page
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

        //没有错误
        if ($form->isValid()) {
            $user_id = $request->getSession()->get('uid');

            $ssi_respondent = new SsiRespondent();
            $ssi_respondent->setUserId($user_id);
            $ssi_respondent->setStatusFlag($values['permission_flag']);
            $em->persist($ssi_respondent);
            $em->flush();

            // permission: yes
            if ($ssi_respondent->needPrescreening()) {
                return $this->redirect($this->generateUrl('_ssi_partner_redirect'));
            } else {
                // permission no : add point
                $point_value = 1;
                $service = $this->get('points_manager');
                $this->givePoint($service, $user_id, $point_value, '申请参与SSI市场调查项目');

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
                $service = $this->get('points_manager');
                $this->givePoint($service, $user_id, $point_value, '申请参与SSI市场调查项目');

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

    public function givePoint($service, $user_id, $point_value, $comment)
    {
        $ad_category_id = AdCategory::ID_QUESTIONNAIRE_EXPENSE;
        $task_type_id = TaskHistory00::TASK_TYPE_SURVEY;
        $service->updatePoints($user_id, $point_value, $ad_category_id, $task_type_id, $comment);
    }
}
