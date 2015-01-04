<?php
namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\BackendBundle\Form\Type\PointsStrategyType;
use Jili\BackendBundle\Form\Type\GameEggsBreakerAuditType;
use Jili\BackendBundle\Form\Type\GameEggsBreaker\OrderIdFilterType;
use Jili\BackendBundle\Form\Type\GameEggsBreaker\OrderFilterType;

/**
 * @Route("/admin/activity/december", requirements={"_scheme" = "https"})
 */
class DecemberActivityController extends Controller implements IpAuthenticatedController 
{
    /**
     * @Route("/search-order-id")
     * @Method( {"GET", "POST"});
     */
    public function orderIdFilterAction()
    {
       $request = $this->get('request'); 
       $form = $this->createForm(new OrderIdFilterType()) ;//, array(

       if ('POST'=== $request->getMethod()) {
           $form->bind( $request);
           if ($form->isValid()) {
               $data = $form->getData();
               $entity = $this->get('doctrine.orm.entity_manager')
                   ->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder' )
                   ->findOneByOrderId($data['orderId']);

               if($entity) {
                   // redirect to audit id action
                   return $this->redirect($this->generateUrl('jili_backend_decemberactivity_audit',
                       array('id'=> $entity->getId() )));
               } else {
                   $this->get('session')->getFlashBag()->add('error', '没找到订单号'.$data['orderId'].'相关记录');
               }
           }
       }

       return $this->render( 'JiliBackendBundle:GameEggsBreaker/TaobaoOrder:filter_order_id.html.twig', array('form'=> $form->createView()));
    }

    /**
     * @Route("/order-filter")
     * @Method( {"GET", "POST"});
     */
    public function orderFilterAction()
    {
        $request = $this->get('request'); 
        $form = $this->createForm(new OrderFilterType());
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $data= $form->getData();
                try{
                    $begin = new \Datetime($data['beginAt']); 
                    $finish = new \Datetime($data['finishAt']); 
                    if($finish >  $begin  )  { 
                        return $this->redirect( $this->generateUrl('jili_backend_decemberactivity_listall',
                            array( 'beginAt'=> $data['beginAt'],
                            'finishAt'=> $data['finishAt'],
                            'auditStatus'=> $data['auditStatus'])));
                    } else {
                        $this->get('session')->getFlashBag()->add('error', '结束时间 要晚于 开始时间');
                    }
                } catch(\Exception $e) {
                    $this->get('session')->getFlashBag()->add('error', $e->getMessage());
                } 
            }
        }
        return $this->render( 'JiliBackendBundle:GameEggsBreaker/TaobaoOrder:filter_order.html.twig', array('form'=> $form->createView()));
    }

    /**
     * @Route("/list-orders/{p}",  defaults={"p"=1}, requirements={"p" = "\d*"})
     * @Method( "GET");
     */
    public function listAllAction($p)
    {
       $request = $this->get('request'); 
       $page_size = $this->container->getParameter('page_num');

       $em = $this->get('doctrine.orm.entity_manager');

       $filters = array();

       if($request->query->has('finishAt') && $request->query->has('beginAt') ) {
           $filters['finish'] = new \Datetime($request->query->get('finishAt'));
           $filters['begin']  = new \Datetime( $request->query->get('beginAt'));
       }
       
       if($request->query->has('auditStatus')) {
           $filters['auditStatus'] = $request->query->get('auditStatus');
       }

       $returns = $em->getRepository( 'JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
           ->fetchByRange( $p , $page_size, $filters  );

       return $this->render('JiliBackendBundle:GameEggsBreaker\TaobaoOrder:list.html.twig', array(
           'entities'=> $returns['data'],
           'total'=> $returns['total'] ,
           'page_size'=> $page_size,
           'p'=>$p));
    }

    /**
     * @Route("/audit-order/{id}",  requirements={"id" = "\d+"})
     * @Method( {"GET", "POST" });
     */
    public function auditAction($id)
    {

        $request = $this->get('request');
        $em = $this->get('doctrine.orm.entity_manager');
        $entity = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')->findOneForAudit($id);

        if(! $entity)  {
            $this->get('session')->getFlashBag()->add('error', '没找到审核订单');
            return $this->redirect( $this->generateUrl('jili_backend_decemberactivity_listall'));
        } 
        $user = $em->getRepository('JiliApiBundle:User')
            ->findOneById($entity->getUserId());
        $form = $this->createForm( new GameEggsBreakerAuditType(), $entity);

        if( 'POST' === $request->getMethod()) {
            $form->bind($request);
            if($form->isValid()  ) {
                $data = $form->getData();
                $this->get('december_activity.game_eggs_breaker')
                    ->auditOrderEntity($data);

                $this->get('session')->setFlash('notice','审核成功');
                return $this->redirect($this->generateUrl('jili_backend_decemberactivity_listall'));
            }
        }

        return $this->render('JiliBackendBundle:GameEggsBreaker/TaobaoOrder:edit.html.twig', array(
            'form'=>$form->createView(),
            'id'=>$id,
            'user'=> $user
        ));
    }

    /**
     * @Route("/points-pool-publised-sccuess")
     * @Method("GET");
     */
    public function pointsPoolpulishedSuccess() 
    {
        return $this->render('JiliBackendBundle:GameEggsBreaker:PointsSuccuess.html.twig');
    }

    /**
     * @Route("/build-consolation-points-strategy")
     * @Method({"GET", "POST"});
     */
    public function consolationPointsPoolAction() 
    {
       $request = $this->get('request'); 
       $form = $this->createForm( new PointsStrategyType());
       if('POST'=== $request->getMethod()){
           $form->bind($request);
           if($form->isValid()) {
               $data = $form->getData();
               try {
                   $this->get('december_activity.game_eggs_breaker')->publishPointsStrategy( $data['rules'], 'consolation');
                   $this->get('session')->setFlash('notice','发布成功');
                   return $this->redirect( $this->generateUrl('jili_backend_decemberactivity_pointspoolpulishedsuccess') );
               } catch(\Exception $e) {
                   $this->get('logger')->crit('[backend][decemberActivity][consolationPointsPool] points strategy publish internal error. '.$e->getMessage() );
                   $this->get('session')->setFlash('error','发布失败! ');

               }
           }
       }
       return $this->render('JiliBackendBundle:GameEggsBreaker:consolation_points_strategy.html.twig',
           array('title'=> '安慰奖',
           'form'=>$form->createView()));
    }

    /**
     * @Route("/build-common-points-strategy")
     * @Method({"GET", "POST"});
     */
    public function pointsPoolAction()
    {
       $request = $this->get('request'); 
       $form = $this->createForm( new PointsStrategyType());
       if('POST'=== $request->getMethod()){
           $form->bind($request);
           if($form->isValid()) {
               $data = $form->getData();
               try {
                   $this->get('december_activity.game_eggs_breaker')->publishPointsStrategy( $data['rules'], 'common');
                   $this->get('session')->setFlash('notice','发布成功');
                   return $this->redirect( $this->generateUrl('jili_backend_decemberactivity_pointspoolpulishedsuccess') );
               } catch(\Exception $e) {
                   $this->get('logger')->crit('[backend][decemberActivity][commonPointsPool] points strategy publish internal error. '.$e->getMessage() );
                   $this->get('session')->setFlash('error','发布失败! ');

               }
           }
       }
       return $this->render('JiliBackendBundle:GameEggsBreaker:common_points_strategy.html.twig',
           array('title'=> '',
           'form'=>$form->createView()));

    }

}
