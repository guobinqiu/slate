<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jili\BackendBundle\Validator\Constraints\GameSeekerRules;

/**
 * @Route("/admin/game-seeker", requirements={"_scheme" = "https"})
 */
class GameSeekerController extends Controller implements IpAuthenticatedController 
{
    /**
     * @Route("/build-points-strategy")
     *   step1. upload a points pool  strategy
     *   textarea form post
     */
    public function buildPointStrategyAction()
    {
        $form = $this->createFormBuilder()
            ->add('rules', 'textarea', array('label'=>''))
            ->getForm();
        $logger = $this->get('logger');
        // add a check box , 是否立即发布
        $request = $this->get('request');
        if( 'POST' === $request->getMethod()) {
            $form->bind($request);
            $data = $form->getData();
            if($form->isValid()) {
                // validate
                $errorList = $this->get('validator')->validateValue($data['rules'],new  GameSeekerRules());
                if (count($errorList) == 0) {
                    $this->get('doctrine.orm.default_entity_manager')
                        ->getRepository('JiliBackendBundle:GameSeekerPointsPool')
                        ->batchInsertRules( $data['rules'] );
                    $this->get('session')->getFlashBag()->add ('notice','规则成功保存!');
                    return $this->redirect($this->generateUrl('jili_backend_gameseeker_publishpointsstrategy' ));
                } else {
                    $error_message = $errorList[0]->getMessage();
                    $this->get('session')->getFlashBag()->add ('error',$error_message);
                }
            }
        }
        return $this->render('JiliBackendBundle:GameSeeker/PointsStrategy:build.html.twig',
            array('form'=> $form->createView()));
    }

    /**
     * @Route("/publish-points-strategy")
     *
     * setp3. query the points strategy to cache config.
     *      a cache config file app/cache_data/prod/game_seeker_pool_config_timestamp_created).txt
     */
    public function publishPointsStrategyAction()
    {
        $request = $this->get('request'); 
        $form = $this->createFormBuilder()
            ->add('is_immediate', 'hidden')
            ->getForm();

        $logger = $this->get('logger');

        if('POST'=== $request->getMethod()) {

            $num_rules = $this->get('doctrine.orm.default_entity_manager')
                ->getRepository('JiliBackendBundle:GameSeekerPointsPool')
                ->batchSetPublished();

            if( count($num_rules) > 0  ) {
                $this->get('game_seeker.points_pool')->publish();
                $this->get('session')->getFlashBag()->add ('notice','找宝箱奖分规则发布成功');
                return $this->redirect($this->generateUrl('jili_backend_gameseeker_operatesuccess' ));
            }  else {
                $this->get('session')->getFlashBag()->add ('error','发布无效, 当前数据表中没有需要发布的方案');
            }

        }

        $rules = $this->get('doctrine.orm.default_entity_manager')
            ->getRepository('JiliBackendBundle:GameSeekerPointsPool')
            ->fetchToPublish();
        return $this->render('JiliBackendBundle:GameSeeker/PointsStrategy:publish.html.twig', array('form'=> $form->createView(), 'rules'=> $rules));
    }

    /**
     * @Route("/manage-chest")
     */
    public function manageChestAction()
    {
        $logger = $this->get('logger');

        $current_chest_quantity = $this->get('game_seeker.points_pool')->fetchChestCount();
        $form = $this->createFormBuilder( array('quantity'=> $current_chest_quantity) )
            ->add('quantity', 'number', array('label'=> '宝箱数量'))
            ->getForm();

        $request = $this->get('request');
        if( 'POST'=== $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $form_data = $form->getData();
                try {
                    $this->get('game_seeker.points_pool')->updateChestCount($form_data['quantity']);
                    $this->get('session')->getFlashBag()->add ('notice','宝箱个数设置成功!');
                    return $this->redirect($this->generateUrl('jili_backend_gameseeker_operatesuccess'));
                } catch(\Exception $e) {
                    $this->get('logger')->crit($e->getMessage());    
                    // session flash
                    $this->get('session')->getFlashBag()->add ('error',$error_message);
                }
            }
        }

        return $this->render('JiliBackendBundle:GameSeeker/PointsStrategy:chest.html.twig', array(
            'form'=> $form->createView()
        ));
    }

    /**
     * @Route("/success")
     */
    public function operateSuccessAction()
    {
        return $this->render('JiliBackendBundle:GameSeeker/PointsStrategy:operate_success.html.twig');
    }

    /**
     * @Route("/enable-points-strategy/{$created_at}")
     * step2. confirm the latest created points strategy. set is_valid = true, update others to false 
     */
    public function enablePointsStrategyAction()
    {
        $logger = $this->get('logger');
        $request = $this->get('request');
        $form = $this->createFormBuilder()
            ->add('enable', 'hidden')
            ->getForm();
        if( 'POST' === $request->getMethod()) {
            $form->bind($request);
            if($form->isValid()){

                $this->get('doctrine.orm.default_entity_manager')
                    ->getRepository('JiliBackendBundle:GameSeekerPointsPool')
                    ->batchSetEnable();

                return $this->redirect($this->generateUrl('jili_backend_gameseeker_publishpointsstrategy' ));
            }
        }
        return $this->render('JiliBackendBundle:GameSeeker/PointsStrategy:publish.html.twig',
            array('form'=> $form->createView()));
    }
}
