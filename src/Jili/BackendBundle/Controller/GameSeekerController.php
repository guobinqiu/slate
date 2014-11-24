<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jili\BackendBundle\Validator\Constraints\GameSeekerRules;

/**
 * @Route("/admin/game-seeker", requirements={"_scheme" = "https"})
 */
class GameSeekerController extends Controller
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
            $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__ ,'$data','')). var_export($data, true) );
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
                    $errorMessage = $errorList[0]->getMessage();
                    $this->get('session')->getFlashBag()->add ('error',$errorMessage);
                }
            }
        }
        return $this->render('JiliBackendBundle:GameSeeker/PointsStrategy:build.html.twig', array('form'=> $form->createView()));
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
                // write the the cache file
                $this->get('game_seeker.points_pool')->publish();
                return $this->redirect($this->generateUrl('jili_backend_gameseeker_operatesuccess' ));
            }  else {
                // 
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
        // code...
        $form = $this->createFormBuilder()
            ->add('total', 'number' , array('label'=> '宝箱数量'))
            ->getForm();
        $request = $this->get('request');
        if( 'POST'=== $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
            }

            // session flash
            return $this->redirect('jili_backend_gameseeker_operatesuccess');
        }
        return $this->render('JiliBackendBundle:GameSeeker:Chest.html.twig', array(
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
                    ->batchSetEnable( );

                return $this->redirect($this->generateUrl('jili_backend_gameseeker_publishpointsstrategy' ));
            }
        }

        //$logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__ ,'$data','')). var_export($data, true) );
        return $this->render('JiliBackendBundle:GameSeeker/PointsStrategy:publish.html.twig', array('form'=> $form->createView()));
    }
}
