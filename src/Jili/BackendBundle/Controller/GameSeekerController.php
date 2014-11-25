<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Jili\BackendBundle\Validator\Constraints\GameSeekerRules;
/**
 * @Route("/admin/game-seeker")
 */
class GameSeekerController extends Controller
{
    /**
     * @Route("/build-points-strategy")
     * @Template()
     */
    public function buildPointStrategyAction()
    {
        $logger = $this->get('logger');
        //$logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__ ,'$data','')). var_export($data, true) );
        // step1. upload a points pool  strategy
        // textarea form post
        $form = $this->createFormBuilder()
            ->add('rules', 'textarea', array('label'=>''))
            ->getForm();

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
                        ->batchInsertRules( $data['rules']);
                    return $this->redirect($this->generateUrl('jili_backend_gameseeker_enable' ));
                } else {
                    $errorMessage = $errorList[0]->getMessage();
                    // set flash error messages.
                }
            }
        }
        return $this->render('JiliBackendBundle:GameSeeker/PointsStrategy:build.html.twig', array('form'=> $form->createView()));
    }

    /**
     * @Route("/enable")
     * @Template()
     */
    public function enableAction()
    {
        // step2. confirm the latest created points strategy. set is_valid = true, update others to false 
                    //$logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__ ,'rules','')). var_export($data['rules'], true) );
        // update set is_valid by created_at and ids  
    }

    /**
     * @Route("/publish")
     * @Template()
     */
    public function publishAction()
    {
        //setp3. query the points strategy to cache config.
        // a cache config file app/cache_data/prod/game_seeker_pool_config_{timestamp_created).txt
    }
//
//    /**
//     * @Route("/monitor")
//     * @Template()
//     */
//    public function monitorAction()
//    {
//        // advanced usage
//        // query the game_seeker_daily.
//        // point_history_xx
//        // game_seeker_pool_YYYYMMDD_usage.txt
//        //
//    }
//
//    /**
//     * @Route("/adjust")
//     * @Template()
//     */
//    public function adjustAction()
//    {
//        // advanced usage
//    }
//
//    /**
//     * @Route("/review")
//     * @Template()
//     */
//    public function reviewAction()
//    {
//        // advanced usage
//    }
}
