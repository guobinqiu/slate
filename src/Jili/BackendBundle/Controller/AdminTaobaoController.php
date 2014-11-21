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

use Jili\FrontendBundle\Entity\TaobaoComponent;

/**
 * @Route("/admin/taobao",requirements={"_scheme"="https"})
 */
class AdminTaobaoController extends Controller {

    /**
    * @Route("/component", name="_admin_taobao_component")
    */
    public function componentAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        $componentId = $request->query->get('componentId', TaobaoComponent :: DROP_DOWN_BOX_DEFAULT);
        $keywordId = $request->query->get('keywordId', TaobaoComponent :: DROP_DOWN_BOX_DEFAULT);
        $categoryId = $request->query->get('categoryId', TaobaoComponent :: DROP_DOWN_BOX_DEFAULT);

        //组件类型下拉框
        $component_category = $this->getComponentCategory();

        //产品分类下拉框
        $categorys = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findAll();

        //取得组件list
        $param = $this->getConditions($componentId, $categoryId, $keywordId);

        //save condition to session
        $session = $request->getSession();
        $session->set('admin_taobao_condition', $param);

        $components = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findComponentsByCondition($param);
        $arr['component_category'] = $component_category;
        $arr['categorys'] = $categorys;
        $arr['componentId'] = $componentId;
        $arr['keywordId'] = $keywordId;
        $arr['categoryId'] = $categoryId;
        $arr['category_product'] = TaobaoComponent :: TAOBAO_COMPONENT_KEYWORD;
        $arr['components'] = $components;
        $arr['drop_down_box_default'] = TaobaoComponent :: DROP_DOWN_BOX_DEFAULT;
        return $this->render('JiliBackendBundle:taobao:component.html.twig', $arr);
    }

    public function getConditions($componentId, $categoryId, $keywordId) {
        $param['componentId'] = $componentId;
        if ($componentId == TaobaoComponent :: TAOBAO_COMPONENT_KEYWORD) {
            //分类产品场合
            $param['categoryId'] = $categoryId;
            $param['keywordId'] = $keywordId;
            if ($keywordId == TaobaoComponent :: DROP_DOWN_BOX_DEFAULT) {
                //关键字为全部
                $param['keywordId'] = null;
            }
        } else {
            //分类产品除外场合
            $param['categoryId'] = null;
            $param['keywordId'] = null;
        }

        return $param;
    }

    //取得组件类型数组
    public function getComponentCategory() {
        $component_category = array (
            TaobaoComponent :: TAOBAO_COMPONENT_SEARCH_BOX => '搜索框',
            TaobaoComponent :: TAOBAO_COMPONENT_KEYWORD => '分类产品',
            TaobaoComponent :: TAOBAO_COMPONENT_ITEM => '单品',
            TaobaoComponent :: TAOBAO_COMPONENT_SHOP => '店铺'
        );
        return $component_category;
    }

    /**
    * @Route("/keywords", name="_admin_taobao_keywords")
    */
    public function keywordsAction() {
        $category_id = $this->get('request')->query->get('categoryId');

        //取得某个类别下的所有关键字list
        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findKeywordByCategoryId($category_id);
        $logger = $this->get('logger');

        $response = new JsonResponse();
        $response->setData($keywords);
        return $response;
    }

    /**
    * @Route("/saveComponent", name="_admin_taobao_saveComponent")
    */
    public function saveComponentAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        //取得组件ID
        $id = $request->query->get('id', "");

        //defautl value
        $componentId = TaobaoComponent :: DROP_DOWN_BOX_DEFAULT;
        $categoryId = TaobaoComponent :: DROP_DOWN_BOX_DEFAULT;
        $keyword = "";
        $content = "";

        //check 组件ID, if (id is exist, get component record)
        $taobaoComponent = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneById($id);
        if ($taobaoComponent) {
            $componentId = $taobaoComponent->getComponentId();
            $categoryId = $taobaoComponent->getCategoryId();
            $keyword = $taobaoComponent->getKeyword();
            $content = $taobaoComponent->getContent();
        }

        //组件类型下拉框
        $component_category = $this->getComponentCategory();

        //产品分类下拉框
        $categorys = $em->getRepository('JiliFrontendBundle:TaobaoCategory')->findAll();

        // show saveComponent page
        $arr['component_category'] = $component_category;
        $arr['categorys'] = $categorys;
        $arr['componentId'] = $componentId;
        $arr['categoryId'] = $categoryId;
        $arr['keyword'] = $keyword;
        $arr['content'] = $content;
        $arr['id'] = $id;
        $arr['category_product'] = TaobaoComponent :: TAOBAO_COMPONENT_KEYWORD;
        $arr['drop_down_box_default'] = TaobaoComponent :: DROP_DOWN_BOX_DEFAULT;
        return $this->render('JiliBackendBundle:taobao:saveComponent.html.twig', $arr);
    }

    /**
    * @Route("/saveComponentFinish", name="_admin_taobao_saveComponentFinish")
    */
    public function saveComponentFinishAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        // get post data
        $componentId = $request->request->get('componentId', TaobaoComponent :: DROP_DOWN_BOX_DEFAULT);
        $categoryId = $request->request->get('categoryId', TaobaoComponent :: DROP_DOWN_BOX_DEFAULT);
        $keyword = $request->request->get('keyword', "");
        $content = $request->request->get('content', "");
        $id = $request->request->get('id', "");

        // check component content exist
        $taobaoComponent = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneById($id);
        // 保存组件内容
        if ($taobaoComponent) {
            $taobaoComponent->setUpdatedAt(new \ DateTime());
        } else {
            $taobaoComponent = new TaobaoComponent();
        }
        $taobaoComponent->setComponentId($componentId);
        $taobaoComponent->setCategoryId($categoryId);
        $taobaoComponent->setKeyword($keyword);
        $taobaoComponent->setContent($content);
        $em->persist($taobaoComponent);
        $em->flush();

        //生成默认搜索条件(query condition)，并跳转到一览页面
        $parameters = $this->defaultCondition($componentId, $categoryId);
        return $this->redirect($this->generateUrl('_admin_taobao_component', $parameters));
    }

    public function defaultCondition($componentId, $categoryId) {
        $parameters['componentId'] = $componentId;
        if ($categoryId) {
            $parameters['categoryId'] = $categoryId;
        }
        return $parameters;
    }

    /**
    * @Route("/deleteComponent/{id}", name="_admin_taobao_deleteComponent")
    */
    public function deleteComponentAction($id) {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        //删除组件
        $taobaoComponent = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneById($id);
        if ($taobaoComponent) {
            $em->remove($taobaoComponent);
            $em->flush();
        }

        //生成默认搜索条件(query condition)，并跳转到一览页面
        $session = $request->getSession();
        $parameters = $session->get('admin_taobao_condition');
        return $this->redirect($this->generateUrl('_admin_taobao_component', $parameters));
    }

    /**
    * @Route("/sortComponent", name="_admin_taobao_sortComponent")
    */
    public function sortComponentAction() {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $datas = $request->request;
        $components = array ();
        $i = 0;
        //get data
        foreach ($datas as $key => $value) {
            $name = explode("_", $key);
            $components[$i]['id'] = $name[1];
            $components[$i]['sort'] = $value;
            $i++;
        }

        //update sort
        foreach ($components as $component) {
            $taobaoComponent = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findOneById($component['id']);
            if ($taobaoComponent) {
                $taobaoComponent->setSort($component['sort']);
                $taobaoComponent->setUpdatedAt(new \ DateTime());
                $em->persist($taobaoComponent);
                $em->flush();
            }
        }

        //生成默认搜索条件(query condition)，并跳转到一览页面
        $session = $request->getSession();
        $parameters = $session->get('admin_taobao_condition');
        return $this->redirect($this->generateUrl('_admin_taobao_component',$parameters));
    }
}