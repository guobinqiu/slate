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
use Jili\BackendBundle\Form\Type\VoteType;
use Jili\ApiBundle\Entity\Vote;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Jili\ApiBundle\Utility\FileUtil;

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
     * @Route("/add", name="_admin_vote_add")
     */
    public function addAction(Request $request)
    {
        return $this->render('JiliBackendBundle:Vote:add.html.twig');
    }

    /**
     * @Route("/edit", name="_admin_vote_edit")
     */
    public function editAction(Request $request)
    {
        return $this->render('JiliBackendBundle:Vote:edit.html.twig');
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