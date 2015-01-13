<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\FrontendBundle\Form\Type\GameEggsBreakerTaoBaoOrderType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Jili\BackendBundle\Utility\TaobaoOrderToEggs;
use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;
use Jili\FrontendBundle\Entity\TaobaoCategory;

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
        $session =$this->get('session');
        if(! $session->has('uid')) {
            $session->set('referer', $this->get('request')->getRequestUri());
        }

        $em  = $this->get('doctrine.orm.entity_manager');

        $products = $em->getRepository('JiliFrontendBundle:TaobaoSelfPromotionProducts')
            ->fetch();
        $categorys = $em->getRepository('JiliFrontendBundle:TaobaoCategory')
            ->findCategorys(0, TaobaoCategory::SELF_PROMOTION);


        return $this->render('JiliFrontendBundle:DecemberActivity:index.html.twig',array('products'=>$products , 'categorys'=> $categorys) );
    }

    /**
     * render the ranking 
     * @Route("/eggs-sent-stat")
     * @Method("GET")
     */
    public function eggsSentStatAction()
    {
        $stat = $this->get('december_activity.game_eggs_breaker')->fetchSentStat();
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
        $form = $this->createForm(new GameEggsBreakerTaoBaoOrderType());
 
        if( 'POST' == $request->getMethod()) {
            if( ! $session->has('uid')) {
                $session->set('goToUrl', $this->get('router')->generate('jili_frontend_decemberactivity_index'));
                return $this->redirect($this->generateUrl('_login') );
            }

            $form->bind($request);
            if( $form->isValid()){
                $data= $form->getData();

                $entity = new GameEggsBreakerTaobaoOrder();
                $entity->setUserId($session->get('uid'))
                    ->setOrderId($data['orderId'])
                    ->setOrderAt($data['orderAt']);
                $validator = $this->get('validator');
                $errors = $validator->validate($entity);
                if(count($errors)>0) {
                    foreach($errors as $error ) {
                        $messages[] = $error->getMessage();
                        $this->get('session')->setFlash('error', $messages);
                    }
                } else {
                    $em  = $this->get('doctrine.orm.entity_manager');
                    $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
                        ->insertUserPost( array('userId'=>$session->get('uid'),
                            'orderAt'=> new \Datetime($data['orderAt']), 
                            'orderId'=>$data['orderId'], 
                        ));
                    $this->get('session')->setFlash('notice','提交成功，等待审核');
                }
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
        $startAt = new \Datetime('2015-01-13 15:30:00');
        $endAt = new \Datetime('2015-01-13 15:40:00');
        $now = new \Datetime();

        $response->setData( array('code'=> 0, 
            'data'=>array(
                'token'=> $record->getToken(),
                'numOfEggs'=> $record->getNumOfCommon(),
                'numOfConsolationEggs' => $record->getNumOfConsolation(),
                'lessForNextEgg'=> TaobaoOrderToEggs::lessToNext( $record->getTotalPaid()),
                'isOpenSeason'=> ( $now < $startAt || $now > $endAt) ? false: true 
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

        $startAt = new \Datetime('2015-01-13 15:30:00');
        $now = new \Datetime();
        if ('dev' !==  $this->get('kernel')->getEnvironment() && 
            $now >= $startAt )  {
                return $response;
            }


        $result = $this->get('december_activity.game_eggs_breaker')
            ->breakEgg(array(
                'user_id'=> $this->get('session')->get('uid'),
                'token'=> $request->request->get('token'),
                'egg_type'=>$request->request->get('egg_type')
            ) );

            if(!is_null($result)){
                $response->setData($result);
            }
        return $response;
    }

    /**
     * @Route("/broken-stat")
     * @Method("GET")
     */
    public function brokerListAction() 
    {
        $stat = $this->get('december_activity.game_eggs_breaker')->fetchBrokenStat();
        return $this->render('JiliFrontendBundle:DecemberActivity:recent_brokers_stat.html.twig', $stat );
    }
}
