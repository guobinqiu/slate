<?php
namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\BackendBundle\Form\Type\PointsStrategyType;

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
     * @Route("/list-orders")
     * @Method( "GET");
     */
    public function listAllAction($page=1)
    {
       $request = $this->get('request'); 
       $logger = $this->get('logger');

       $page_size= 30;


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
