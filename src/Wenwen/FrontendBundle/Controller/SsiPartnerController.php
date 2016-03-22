<?php
namespace Wenwen\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    public function preExecute()
    {
        $request = $this->getRequest();
        $params = explode('::', $request->attributes->get('_controller'));
        $actionName = substr($params[1], 0, -6);
        $em = $this->getDoctrine()->getManager();

        if (in_array($actionName, array (
            'error'
        ))) {
            # do nothing if error or complete page
        } elseif (!$request->getSession()->get('uid')) {
            # ログインしていない
            $this->get('request')->getSession()->set('errors', array (
                'panelist_is_not_authenticated' => true
            ));

            return new RedirectResponse($this->generateUrl('_ssi_partner_error'));
        }

        $user_id = $request->getSession()->get('uid');
        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user_id);

        $this->ssi_respondent = $ssi_respondent;

        if (in_array($actionName, array (
            'permission',
            'commit'
        ))) {
            // 同意未回答
            if (!$ssi_respondent) {
                # pass them through
            } elseif ($ssi_respondent->needPrescreening()) {
                // 属性アンケート未回答の人 -> ページヘ遷移
                return new RedirectResponse($this->generateUrl('_ssi_partner_prescreen'));
            } else {
                // 回答済み
                $this->get('request')->getSession()->set('errors', array (
                    'panelist_has_already_answered' => true
                ));

                return new RedirectResponse($this->generateUrl('_ssi_partner_error'));
            }
        } elseif (in_array($actionName, array (
            'prescreen',
            'redirect',
            'prescreeningComplete'
        ))) {

            // 資格なし
            if (!$ssi_respondent || !$ssi_respondent->needPrescreening()) {

                $this->get('request')->getSession()->set('errors', array (
                    'panelist_has_already_answered' => true
                ));

                return new RedirectResponse($this->generateUrl('_ssi_partner_error'));
            }
        }

        return null;
    }

    /**
     * @Route("/permission", name="_ssi_partner_permission")
     * @Template
     */
    public function permissionAction(Request $request)
    {
        $response = $this->preExecute();
        if ($response) {
            return $response;
        }

        $form = $this->createForm(new SsiPartnerPermissionType());

        return $this->render('WenwenFrontendBundle:SsiPartner:permission.html.twig', array (
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/commit", name="_ssi_partner_commit")
     * @Template
     */
    public function commitAction(Request $request)
    {
        $response = $this->preExecute();
        if ($response) {
            return $response;
        }

        $em = $this->getDoctrine()->getManager();

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
        $response = $this->preExecute();
        if ($response) {
            return $response;
        }

        return $this->render('WenwenFrontendBundle:SsiPartner:complete.html.twig');
    }

    /**
     * @Route("/prescreen", name="_ssi_partner_prescreen")
     * @Template
     */
    public function prescreenAction(Request $request)
    {
        $response = $this->preExecute();
        if ($response) {
            return $response;
        }

        return $this->render('WenwenFrontendBundle:SsiPartner:prescreen.html.twig');
    }

    /**
     * @Route("/prescreeningComplete", name="_ssi_partner_prescreeningcomplete")
     * @Template
     */
    public function prescreeningCompleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $response = $this->preExecute();
        if ($response) {
            return $response;
        }

        // 念入り
        if ($this->ssi_respondent->needPrescreening()) {
            // ステータスを書き換えてポイント付与
            $this->ssi_respondent->setStatusFlag(SsiRespondent::STATUS_PRESCREENED);
            $em->persist($this->ssi_respondent);
            $em->flush();

            // add point
            $point_value = 1;
            $user_id = $request->getSession()->get('uid');
            $service = $this->get('points_manager');
            $this->givePoint($service, $user_id, $point_value, '申请参与SSI市场调查项目');
        }

        return $this->render('WenwenFrontendBundle:SsiPartner:complete.html.twig');
    }

    /**
     * @Route("/redirect", name="_ssi_partner_redirect")
     * @Template
     */
    public function redirectAction(Request $request)
    {
        $response = $this->preExecute();
        if ($response) {
            return $response;
        }

        return $this->redirect($this->ssi_respondent->getPrescreeningSurveyUrl());
    }

    /**
     * @Route("/error", name="_ssi_partner_error")
     * @Template
     */
    public function errorAction(Request $request)
    {
        $response = $this->preExecute();
        if ($response) {
            return $response;
        }
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
