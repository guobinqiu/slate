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

use Jili\BackendBundle\Form\Type\PromotionSelfLinkProductType;
use Jili\FrontendBundle\Entity\TaobaoComponent;
use Jili\FrontendBundle\Entity\TaobaoSelfPromotionProducts;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Jili\ApiBundle\Utility\FileUtil;

/**
 * @Route("/admin/taobao",requirements={"_scheme"="https"})
 */
class AdminTaobaoController extends Controller implements  IpAuthenticatedController
{

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
        $component_category = TaobaoComponent :: $COMPONENT_CATEGORY;

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
        return $this->render('JiliBackendBundle:Taobao:component.html.twig', $arr);
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

    /**
    * @Route("/keywords", name="_admin_taobao_keywords")
    */
    public function keywordsAction() {
        $category_id = $this->get('request')->query->get('categoryId');

        //取得某个类别下的所有关键字list
        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->findKeywordByCategoryId($category_id);

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
        $taobaoComponent = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->find($id);
        if ($taobaoComponent) {
            $componentId = $taobaoComponent->getComponentId();
            $categoryId = $taobaoComponent->getCategoryId();
            $keyword = $taobaoComponent->getKeyword();
            $content = $taobaoComponent->getContent();
        }

        //组件类型下拉框
        $component_category = TaobaoComponent :: $COMPONENT_CATEGORY;

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
        return $this->render('JiliBackendBundle:Taobao:saveComponent.html.twig', $arr);
    }

    /**
    * @Route("/saveComponentFinish", name="_admin_taobao_saveComponentFinish")
    * @Method("POST");
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
        $taobaoComponent = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->find($id);
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
        $taobaoComponent = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->find($id);
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
            $taobaoComponent = $em->getRepository('JiliFrontendBundle:TaobaoComponent')->find($component['id']);
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

    /**
     * @Route("/promotion-self-product/new")
     * @Method( "GET" )
     */
    public function newPromotionSelfProductAction()
    {
        $form = $this->createForm( new PromotionSelfLinkProductType() , new TaobaoSelfPromotionProducts());
        return $this->render( 'JiliBackendBundle:Taobao/PromotionSelfProduct:new.html.twig',
            array('form'=> $form->createView() )) ;
    }

    /**
     * @Route("/promotion-self-product/create")
     * @Method( "POST" )
     */
    public function createPromotionSelfProductAction()
    {
        $request = $this->get('request');
        $entity = new TaobaoSelfPromotionProducts();
        $form = $this->createForm( new PromotionSelfLinkProductType(),$entity );
        $form->bind($request);
        if($form->isValid()) {
            $entity = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $uploaded =  $form['picture']->getData();
            if( ! is_null($uploaded)) {
                $picture_name = FileUtil::moveUploadedFile($uploaded,
                    $this->container->getParameter('taobao_self_promotion_picture_dir'));
                $entity->setPictureName($picture_name);
            }

            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', '成功创建商品: '. $entity->getTitle());
            return $this->redirect($this->generateUrl('jili_backend_admintaobao_editpromotionselfproduct', array('id' => $entity->getId())));

        }

        return $this->render( 'JiliBackendBundle:Taobao/PromotionSelfProduct:new.html.twig', array('form'=> $form->createView() )) ;
    }


    /**
     * @Route("/promotion-self-product/list/{p}", defaults={"p"=1}, requirements={"p" = "\d+"})
     * @Method( "GET");
     */
    public function listPromotionSelfProductAction($p)
    {
        $page_size = $this->container->getParameter('page_num');
        $em = $this->getDoctrine()->getManager();
        $returns =   $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->fetchByRange($p, $page_size) ;
    
        return $this->render( 'JiliBackendBundle:Taobao/PromotionSelfProduct:list.html.twig', array(
            'entities'=> $returns['data'],
            'total'=> $returns['total'] ,
            'page_size'=> $page_size,
            'p'=>$p));
    }

    /**
     * @Route("/promotion-self-product/edit/{id}", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function editPromotionSelfProductAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TaobaoSelfPromotionProducts entity.');
        }

        $editForm = $this->createForm( new PromotionSelfLinkProductType(), $entity );
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('JiliBackendBundle:Taobao/PromotionSelfProduct:edit.html.twig' , array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));

    }

    /**
     * @Route("/promotion-self-product/update/{id}", requirements={"id"="\d+"})
     * @Method("PUT")
     */
    public function updatePromotionSelfProductAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TaobaoSelfPromotionProducts entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm( new PromotionSelfLinkProductType(), $entity );
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $uploaded =  $editForm['picture']->getData();
            if( ! is_null($uploaded)) {
                $picture_name = FileUtil::moveUploadedFile($uploaded,
                    $this->container->getParameter('taobao_self_promotion_picture_dir'));
                $entity->setPictureName($picture_name);
            }

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('notice', '成功修改 id为'. $id. '的商品');
            return $this->redirect($this->generateUrl('jili_backend_admintaobao_editpromotionselfproduct', array('id' => $id)));
        }

        return $this->render('JiliBackendBundle:Taobao/PromotionSelfProduct:edit.html.twig' , array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/promotion-self-product/delete/{id}", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deletePromotionSelfProductAction(Request $request, $id)
    {
        //do the remove stuff
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TaobaoSelfPromotionProducts entity.');
            }

            $picture_name = $entity->getPictureName();
            if( ! empty($picture_name) ) {
                $fs = new Filesystem();
                $target = $this->container->getParameter('taobao_self_promotion_picture_dir').$picture_name;
                if ($fs->exists($target) ) {
                    $fs->remove($target);
                }
            };

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', '成功删除 id为'. $id. '的商品');
        }
        return $this->redirect($this->generateUrl('jili_backend_admintaobao_listpromotionselfproduct'));
    }

    /**
     * Creates a form to delete a TaobaoSelfPromotionProducts entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }


}
