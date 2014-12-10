<?php
namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\BackendBundle\Form\Type\PointsStrategyType;
use Jili\BackendBundle\Form\Type\GameEggsBreakerAuditType;

/**
 * @Route("/admin/activity/december", requirements={"_scheme" = "https"})
 */
class DecemberActivityController extends Controller implements IpAuthenticatedController 
{
    /**
     * @Route("/update-order")
     * @Method( "POST");
     */
    public function updateAction()
    {
       $request = $this->get('request'); 
    }

    /**
     * @Route("/list-orders/{p}",  defaults={"p"=1}, requirements={"p" = "\d+"})
     * @Method( "GET");
     */
    public function listAllAction($p)
    {
       $request = $this->get('request'); 
       $logger = $this->get('logger');
       $page_size = $this->container->getParameter('page_num');
       $em = $this->get('doctrine.orm.entity_manager');
       $returns = $em->getRepository( 'JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
           ->fetchByRange( $p , $page_size );

       return $this->render('JiliBackendBundle:GameEggsBreaker\TaobaoOrder:list.html.twig', array('entities'=> $returns['data'], 'total'=> $returns['total'] ,
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

       $form = $this->createForm( new GameEggsBreakerAuditType(), $entity);
        if( 'POST' === $request->getMethod()) {
            if($form->isValid()  ) {
                $data = $form->getData();
                $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
                    ->updateOneOnAudit($data);

                   $this->get('session')->setFlash('notice','审核成功');
                return $this->redirect($this->generateUrl('jili_backend_decemberactivity_listall'));
            }
        }

        return $this->render('JiliBackendBundle:GameEggsBreaker/TaobaoOrder:edit.html.twig', array(
            'form'=>$form->createView(),
            'id'=>$id
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
