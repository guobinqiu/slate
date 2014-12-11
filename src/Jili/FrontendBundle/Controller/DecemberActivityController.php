<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\FrontendBundle\Form\Type\GameEggsBreakerTaoBaoOrderType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Jili\ApiBundle\Entity\AdCategory;
use Jili\BackendBundle\Utility\TaobaoOrderToEggs;

/**
 * @Route("/activity/december")
 */
class DecemberActivityController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('JiliFrontendBundle:DecemberActivity:index.html.twig');
    }

    /**
     * render the ranking 
     * @Route("/eggs-sent-stat")
     * @Method("GET")
     */
    public function eggsSentStatAction()
    {
        $stat = $this->get('december_activity.game_eggs_breaker')->fetchSentStat();
        $logger = $this->get('logger');
        return $this->render('JiliFrontendBundle:DecemberActivity:eggs_sent_stat.html.twig', $stat);
    }

    /**
     * @Route("/add-taobao-order")
     * @Method({"GET","POST"})
     */
    public function addTaobaoOrderAction()
    {
        $request = $this->get('request');
        $session = $this->get('session');
        $logger = $this->get('logger');

        $form = $this->createForm(new GameEggsBreakerTaoBaoOrderType());
        if( 'POST' == $request->getMethod()) {
            if( ! $session->has('uid')) {
                $session->set('goToUrl', $this->get('router')->generate('jili_frontend_decemberactivity_index'));
                return $this->redirect($this->generateUrl('_login') );
            }

            $form->bind($request);
            if( $form->isValid()){
                $data= $form->getData();

                $em  = $this->get('doctrine.orm.entity_manager');
                $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
                    ->insertUserPost( array('userId'=>$session->get('uid'),
                        'orderAt'=> new \Datetime($data['orderAt']), 
                        'orderId'=>$data['orderId'], 
                    ));
                $this->get('session')->setFlash('notice','提交成功，等待审核');
                return $this->redirect($this->generateUrl('jili_frontend_decemberactivity_index'));
            }
        }

        return $this->render( 'JiliFrontendBundle:DecemberActivity:tb_order_form.html.twig' ,
            array('form'=> $form->createView()));
    }

    /**
     * @Route("/get-eggs-info", options={"expose"=true})
     * @Method("POST")
     */
    public function getEggsInfoAction()
    {
        $request = $this->getRequest();
        $response = new  JsonResponse ();
        if(! $request->isXmlHttpRequest()) {
            return $response;
        }

        // user not sign in , return {'code': ?}
        if( ! $this->get('session')->has('uid')) {
            $response->setData(array( 'code'=> 0 ));
            return $response;
        }

        $userId = $this->get('session')->get('uid');
        $em  = $this->get('doctrine.orm.entity_manager');

        $record = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')->findOneByUserId($userId);
        if( ! $record) {
            return $response;
        }

        $record->refreshToken();
        $em->persist($record);
        $em->flush();

        // numOfEggs: 1, numOfConsolationEggs: 3, lessForNextEgg: 00.01 
        //$cost_per_egg = $container->get
        $startAt = new \Datetime('2015-01-20 00:00:00');
        $now = new \Datetime();

        $response->setData( array('code'=> 0, 
            'data'=>array(
                'token'=> $record->getToken(),
                'numOfEggs'=> $record->getNumOfCommon(),
                'numOfConsolationEggs' => $record->getNumOfConsolation(),
                'lessForNextEgg'=> TaobaoOrderToEggs::lessToNext( $record->getTotalPaid()),
                'isStart'=> ( $now >= $startAt ) ? true: false  
            )));

        return $response;      
    }

    /**
     *  request token:  typeOfEgg:0 ,1
     * @Route("/break-egg", options={"expose"=true})
     * @Method("POST")
     * @return {code:0 , data: { points: \d+ } }
     */
    public function breakEggAction()
    {
        // $point = service->breakEgg( $token, );
        $request = $this->getRequest();
        $response = new  JsonResponse ();
        if(! $request->isXmlHttpRequest()) {
            return $response;
        }

        // user not sign in , return {'code': ?}
        if( ! $this->get('session')->has('uid')) {
            $response->setData(array( 'code'=> 0 ));
            return $response;
        }

        return $response;
    }
}
