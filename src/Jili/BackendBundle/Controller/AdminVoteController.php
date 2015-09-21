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
use Jili\ApiBundle\Entity\VoteImage;
use Jili\ApiBundle\Utility\FileUtil;
use Jili\ApiBundle\Utility\ValidateUtil;
use Jili\BackendBundle\Utility\VoteImageResizer;

/**
 * @Route("/admin/vote",requirements={"_scheme"="https"})
 */
class AdminVoteController extends Controller implements IpAuthenticatedController
{

    /**
     * get temp image dir
     *
     */
    public function getTmpImageDir()
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
    public function activeListAction(Request $request, $paging)
    {
        $active_flag = true;
        $page = $request->query->get('page', 1);
        $arr = $this->getVoteList($page, $active_flag);
        $arr['paging'] = $paging;
        $arr['page'] = $page;
        return $this->render('JiliBackendBundle:Vote:activelist.html.twig', $arr);
    }

    /**
     * @Route("/reserveList/{paging}", name="_admin_vote_reserveList")
     */
    public function reserveListAction(Request $request, $paging)
    {
        $active_flag = false;
        $page = $request->query->get('page', 1);
        $arr = $this->getVoteList($page, $active_flag);
        $arr['paging'] = $paging;
        $arr['page'] = $page;
        return $this->render('JiliBackendBundle:Vote:reserveList.html.twig', $arr);
    }

    public function getVoteList($page, $active_flag)
    {
        $page_size = $this->container->getParameter('page_num');

        $em = $this->getDoctrine()->getEntityManager();

        //get vote list
        $result = $em->getRepository('JiliApiBundle:Vote')->fetchVoteList($active_flag);

        foreach ($result as $key => $value) {
            //get vote answer count
            $result[$key]['answerCount'] = $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->getAnswerCount($value['id'], $value['yyyymm']);
            if ($result[$key]['sqPath']) {
                //get sq image path
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

        if ($request->query->has('id')) {
            $vote_id = $request->query->get('id');
            $em = $this->getDoctrine()->getManager();
            //get vote
            $vote = $em->getRepository('JiliApiBundle:Vote')->findOneById($vote_id);

            //set default time
            $vote->setStartTime($vote->getStartTime()->format('Y-m-d'));
            $vote->setEndTime($vote->getEndTime()->format('Y-m-d'));
        } else {
            //set vote choices
            for ($i = 1; $i <= 10; $i++) {
                $VoteChoice = new VoteChoice();
                $vote->addVoteChoice($VoteChoice);
            }
            //set default time
            $vote->setStartTime(date('Y-m-d'));
            $vote->setEndTime(date('Y-m-d'));
            $vote->setPointValue(1);
        }

        //create vote form
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
        $tmp_image = '';

        //set vote choices
        for ($i = 1; $i <= 10; $i++) {
            $VoteChoice = new VoteChoice();
            $vote->addVoteChoice($VoteChoice);
        }

        //create vote form and get form data
        $form = $this->createForm(new VoteType(), $vote);
        $form->bind($request);
        $values = $form->getData();

        //get error messages
        $error_meeeages = $this->getFormErrors($form);

        //todo check period 能否写到VoteType中
        if (!ValidateUtil::validatePeriod($values->getStartTime(), $values->getEndTime())) {
            $error_meeeages[] = 'Invalid period';
        }

        if (!$error_meeeages) {

            //todo: 图片名，wenwen是40位，jili是32位，是否要一致
            $voteImage = $values->getVoteImage();
            if (!is_null($voteImage) && !$voteImage->getError()) {
                //upload image
                $tmp_image = FileUtil::moveUploadedFile($voteImage, $this->getTmpImageDir());
            }

            return $this->render('JiliBackendBundle:Vote:editConfirm.html.twig', array (
                'form' => $form->createView(),
                'values' => $values,
                'tmp_image' => $tmp_image
            ));
        }

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
        $new_flag = true;

        //set vote choices
        for ($i = 1; $i <= 10; $i++) {
            $VoteChoice = new VoteChoice();
            $vote->addVoteChoice($VoteChoice);
        }

        //create vote form and get form data
        $form = $this->createForm(new VoteType(), $vote);
        $form->bind($request);
        $values = $form->getData();

        //get error messages
        $error_meeeages = $this->getFormErrors($form);

        //todo check period 能否写到VoteType中
        if (!ValidateUtil::validatePeriod($values->getStartTime(), $values->getEndTime())) {
            $error_meeeages[] = 'Invalid period';
        }

        if (!$error_meeeages) {
            $em = $this->getDoctrine()->getManager();
            $db_connection = $em->getConnection();
            $db_connection->beginTransaction();

            try {

                if ($values->getId()) {
                    //edit: get vote entity
                    $vote_entity = $em->getRepository('JiliApiBundle:Vote')->findOneById($values->getId());
                    $new_flag = false;
                } else {
                    //add: create vote entity
                    $vote_entity = new Vote();
                    $vote_entity->setYyyymm(date('Ym'));
                }

                //set vote other values
                $vote_entity->setTitle($values->getTitle());
                $vote_entity->setDescription($values->getDescription());
                $vote_entity->setStartTime(\DateTime::createFromFormat('Y-m-d H:i:s', $values->getStartTime() . ' 00:00:00'));
                $vote_entity->setEndTime(\DateTime::createFromFormat('Y-m-d H:i:s', $values->getEndTime() . ' 23:59:59'));
                $vote_entity->setPointValue($values->getPointValue());

                // 月別テーブルをつくる
                $this->generateMonthlyTable($vote_entity);

                $em->persist($vote_entity);
                $em->flush();

                //get vote id
                $vote_id = $vote_entity->getId();

                foreach ($values->getVoteChoices() as $key => $choice) {
                    //get vote choice entity
                    $choice_entity = $em->getRepository('JiliApiBundle:VoteChoice')->findOneBy(array (
                        'voteId' => $vote_id,
                        'answerNumber' => $choice->getAnswerNumber()
                    ));
                    if (!$choice_entity) {
                        // if choice not exist, create VoteChoice entity
                        $choice_entity = new VoteChoice();
                        $choice_entity->setVote($vote_entity);
                        $choice_entity->setAnswerNumber($choice->getAnswerNumber());
                    }
                    //set choice name
                    $choice_entity->setName($choice->getName());
                    $em->persist($choice_entity);
                    $em->flush();
                }
                $db_connection->commit();
            } catch (\Exception $e) {
                $db_connection->rollback();
                echo $e->getMessage();

                return $this->render('JiliBackendBundle:Vote:edit.html.twig', array (
                    'form' => $form->createView(),
                    'error_meeeages' => $e->getMessage()
                ));
            }

            $tmp_image = $request->request->get('tmp_image');

            if ($tmp_image) {
                //remove vote image data
                $images = $em->getRepository('JiliApiBundle:VoteImage')->findByVoteId($vote_id);
                foreach ($images as $image) {
                    $em->remove($image);
                    $em->flush();
                }

                //create VoteImage entity
                $vote_image = new VoteImage();
                $vote_image->setVoteId($vote_id);
                $vote_image->setSrcImagePath($this->getTmpImageDir() . '/' . $tmp_image);
                $vote_image->setFile($this->getTmpImageDir() . '/' . $tmp_image);

                $em->persist($vote_image);
                $em->flush();

                //image resizer
                $source_path = $this->getTmpImageDir() . '/' . $tmp_image;
                $target_dir = $this->container->getParameter('upload_vote_image_dir');
                VoteImageResizer::resizeImage($source_path, $target_dir, $vote_image->getSqPath(), $vote_image::SQ_SIDE);
                VoteImageResizer::resizeImage($source_path, $target_dir, $vote_image->getSPath(), $vote_image::S_SIDE);
                VoteImageResizer::resizeImage($source_path, $target_dir, $vote_image->getMPath(), $vote_image::M_SIDE);
            }
            return $this->render('JiliBackendBundle:Vote:index.html.twig', array (
                'vote_edit_complete' => true,
                'edited_vote_id' => $vote_id,
                'is_new' => $new_flag
            ));
        }

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
        $id = $request->query->get('id');
        $ret_page = $request->query->get('page', '');
        $ret_action = $request->query->get('ret_action', '');

        $em = $this->getDoctrine()->getManager();
        $db_connection = $em->getConnection();
        $db_connection->beginTransaction();

        try {
            //remove vote choice
            $voteChoices = $em->getRepository('JiliApiBundle:VoteChoice')->findByVoteId($id);
            foreach ($voteChoices as $voteChoice) {
                $em->remove($voteChoice);
            }

            //remove vote image
            $voteImages = $em->getRepository('JiliApiBundle:VoteImage')->findByVoteId($id);
            foreach ($voteImages as $voteImage) {
                $em->remove($voteImage);
            }

            //remove vote
            $vote = $em->getRepository('JiliApiBundle:Vote')->find($id);
            if ($vote) {
                $em->remove($vote);
            }

            $em->flush();
            $db_connection->commit();
        } catch (\Exception $e) {
            $db_connection->rollback();
            echo $e->getMessage();
        }

        if ($ret_action == '_admin_vote_activelist') {
            return $this->redirect($this->get('router')->generate('_admin_vote_activelist', array (
                'paging' => true,
                'page' => $ret_page
            )));
        } else if ($ret_action == '_admin_vote_reserveList') {
            return $this->redirect($this->get('router')->generate('_admin_vote_reserveList', array (
                'paging' => true,
                'page' => $ret_page
            )));
        } else {
            return $this->redirect($this->get('router')->generate('_admin_vote_index'));
        }
    }

    /**
     * 月別テーブル作成
     *
     * @param Vote
     */
    public function generateMonthlyTable(Vote $vote)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->createYyyymmTable($vote->getYyyymm());
        return true;
    }

    /**
     * get all form errors
     *
     * @param object $form
     *
     * @return array The error meeeages
     */
    public function getFormErrors($form)
    {
        $error_meeeages = array ();
        $errors = $form->getErrors();
        foreach ($errors as $error) {
            if ($error) {
                $error_meeeages[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $key => $child) {
            $error_tiems = $child->getErrors();
            foreach ($error_tiems as $child_error) {
                if ($child_error) {
                    $error_meeeages[] = $key . ": " . $child_error->getMessage();
                }
            }
        }

        return $error_meeeages;
    }
}