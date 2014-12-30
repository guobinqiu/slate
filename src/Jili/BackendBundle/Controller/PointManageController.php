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

/**
 * @Route("/admin/pointmanage",requirements={"_scheme"="https"})
 */
class PointManageController extends Controller implements IpAuthenticatedController {

    /**
    * @Route("/pointHistorySearch", name="_admin_pointHistorySearch")
    */
    public function pointHistorySearchAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        // 设置默认值
        $start_time = $request->get('start', '');
        $end_time = $request->get('end', '');
        $email = $request->get('email', '');
        $category_id = $request->get('category_id', '');

        // 取得所有积分类型
        $categoryList = $em->getRepository('JiliApiBundle:AdCategory')->getCategoryList();

        // 设置值到页面上
        $arr['start'] = $start_time;
        $arr['end'] = $end_time;
        $arr['email'] = $email;
        $arr['category_id'] = $category_id;
        $arr['categoryList'] = $categoryList;

        //第一次进来或没有输入email
        if (!$email) {
            // 指向页面
            return $this->render('JiliBackendBundle:PointManage:pointHistorySearch.html.twig', $arr);
        }

        // 检查用户是否存在
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);
        // 用户不存在，信息提示
        if (!$user) {
            $arr['message'] = 'The user is not exist.';
            return $this->render('JiliBackendBundle:PointManage:pointHistorySearch.html.twig', $arr);
        }

        // 取得积分历史记录
        $user_id = $user->getId();
        $result = $em->getRepository('JiliApiBundle:PointHistory0' . ($user_id % 10))->pointHistorySearch($user_id, $category_id, $start_time, $end_time);
        if (!$result) {
            $arr['message'] = 'No record.';
            return $this->render('JiliBackendBundle:PointManage:pointHistorySearch.html.twig', $arr);
        }

        // 取得各记录中所对应的积分类型名称
        foreach ($result as $key => $value) {
            $category = $em->getRepository('JiliApiBundle:AdCategory')->find($value['reason']);
            $result[$key]['categoryName'] = $category->getDisplayName();
        }

        // 分页显示
        $paginator = $this->get('knp_paginator');
        $arr['pagination'] = $paginator->paginate($result, $this->get('request')->query->get('page', 1), 20);
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');

        // 指向页面
        return $this->render('JiliBackendBundle:PointManage:pointHistorySearch.html.twig', $arr);
    }
}