<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Jili\BackendBundle\Form\VoteType;
use Jili\ApiBundle\Entity\Vote;
use Jili\ApiBundle\Entity\VoteChoice;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Jili\ApiBundle\Utility\FileUtil;
use Symfony\Component\Validator\Constraints as Assert;
use Jili\ApiBundle\Utility\ValidateUtil;

/**
 * @Route("/admin/vote",requirements={"_scheme"="https"})
 */
class AdminVoteController extends Controller implements IpAuthenticatedController
{

    public static function getTmpImageDir()
    {
        return $this->container->getParameter('upload_tmp_image_dir');
    }

    /**
     * @Route("/index", name="_admin_vote_index")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('JiliBackendBundle:Vote:index.html.twig');
    }

    /**
     * @Route("/activeList/{paging}",  name="_admin_vote_activelist")
     */
    public function ActiveListAction(Request $request, $paging)
    {
        $active_flag = true;
        $page = $request->query->get('page', 1);
        $arr = $this->getVoteList($page, $active_flag);
        $arr['paging'] = $paging;
        return $this->render('JiliBackendBundle:Vote:activelist.html.twig', $arr);
    }

    /**
     * @Route("/reserveList/{paging}", name="_admin_vote_reserveList")
     */
    public function ReserveListAction(Request $request, $paging)
    {
        $active_flag = false;
        $page = $request->query->get('page', 1);
        $arr = $this->getVoteList($page, $active_flag);
        $arr['paging'] = $paging;
        return $this->render('JiliBackendBundle:Vote:reserveList.html.twig', $arr);
    }

    public function getVoteList($page, $active_flag)
    {
        $page_size = $this->container->getParameter('page_num');

        $em = $this->getDoctrine()->getEntityManager();
        $result = $em->getRepository('JiliApiBundle:Vote')->fetchVoteList($active_flag);

        foreach ($result as $key => $value) {
            $result[$key]['answerCount'] = $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->getAnswerCount($value['id'], $value['yyyymm']);
            if ($result[$key]['sqPath']) {
                $result[$key]['sqPath'] = $this->container->getParameter('upload_vote_image_dir') . $result[$key]['sqPath'];
            } else {
                $result[$key]['sqPath'] = false;
            }
        }
        // 分页显示
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator->paginate($result, $page, $page_size);
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');

        return $arr;
    }

    /**
     * @Route("/edit", name="_admin_vote_edit")
     */
    public function editAction(Request $request)
    {
        $vote = new Vote();

        for ($i = 1; $i <= 10; $i++) {
            $VoteChoice = new VoteChoice();
            $vote->addVoteChoice($VoteChoice);
        }

        $form = $this->createForm(new VoteType(), $vote);

        return $this->render('JiliBackendBundle:Vote:edit.html.twig', array (
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/editConfirm", name="_admin_vote_edit_confirm")
     */
    public function editConfirmAction(Request $request)
    {
        $vote = new Vote();

        for ($i = 1; $i <= 10; $i++) {
            $VoteChoice = new VoteChoice();
            $vote->addVoteChoice($VoteChoice);
        }

        $form = $this->createForm(new VoteType(), $vote);
        $form->bind($request);

        if ($form->isValid()) {
            $values = $form->getData();

            return $this->render('JiliBackendBundle:Vote:editConfirm.html.twig', array (
                'form' => $form->createView(),
                'values' => $values
            ));
        }

        $error_meeeages = ValidateUtil::getFormErrors($form);
        return $this->render('JiliBackendBundle:Vote:edit.html.twig', array (
            'form' => $form->createView(),
            'error_meeeages' => $error_meeeages
        ));
    }

    /**
     * @Route("/editCommit", name="_admin_vote_edit_commit")
     */
    public function editCommitAction(Request $request)
    {
        $vote = new Vote();

        for ($i = 1; $i <= 10; $i++) {
            $VoteChoice = new VoteChoice();
            $vote->addVoteChoice($VoteChoice);
        }

        $form = $this->createForm(new VoteType(), $vote);
        $form->bind($request);

        if ($form->isValid()) {
            $values = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $db_connection = $em->getConnection();
            $db_connection->beginTransaction();

            try {

                $vote_entity = new Vote();
                $vote_entity->setTitle($values->getTitle());
                $vote_entity->setDescription($values->getDescription());
                $vote_entity->setYyyymm(date('Ym'));
                $vote_entity->setStartTime(\DateTime::createFromFormat('Y-m-d H:i:s', $values->getStartTime() . ' 00:00:00'));
                $vote_entity->setEndTime(\DateTime::createFromFormat('Y-m-d H:i:s', $values->getEndTime() . ' 23:59:59'));
                $vote_entity->setPointValue($values->getPointValue());
                $em->persist($vote_entity);
                $em->flush();

                $vote_id = $vote_entity->getId();

                foreach ($values->getVoteChoices() as $key => $choice) {
                    $choice->setAnswerNumber($key + 1);
                    $choice->setVoteId($vote_id);
                    //$choice->setVote($vote_entity);

                    echo "<br>line_" . __LINE__ . "_aaaaaaaaaa<pre>";
                    print_r($choice);

                    $em->persist($choice);
                    $em->flush();
                }

                $db_connection->commit();

                return $this->render('JiliBackendBundle:Vote:index.html.twig');
            } catch (\Exception $e) {
                $db_connection->rollback();
                echo $e->getMessage();

                return $this->render('JiliBackendBundle:Vote:edit.html.twig', array (
                    'form' => $form->createView(),
                    'error_meeeages' => $e->getMessage()
                ));
            }
        }

        $error_meeeages = ValidateUtil::getFormErrors($form);

        return $this->render('JiliBackendBundle:Vote:edit.html.twig', array (
            'form' => $form->createView(),
            'error_meeeages' => $error_meeeages
        ));
    }

    /**
     * @Route("/delete", name="_admin_vote_delete")
     */
    public function deleteAction(Request $request)
    {
        return $this->render('JiliBackendBundle:Vote:delete.html.twig');
    }

    /**
     * 月別テーブル作成2
     *
     * @param Vote
     */
    public function generateMonthlyTable(Vote $vote)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('VoteAnswerYyyymm')->createYyyymmTable($vote->getYyyymm());
        return true;
    }
}