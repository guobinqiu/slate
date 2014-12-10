<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\FrontendBundle\Form\Type\GameEggsBreakerTaoBaoOrderType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Jili\ApiBundle\Entity\AdCategory;

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
        // render the page 
        
       
        return $this->render('JiliFrontendBundle:DecemberActivity:index.html.twig');
    }

    /**
     * render the ranking 
     * @Route("/stat")
     * @Method("GET")
     */
    public function statAction()
    {
        //$cache_files = $this->get('container')->getParameter(''); 
        //$ranking = 
        //$this->get('december_activity.game_eggs_breaker')
        //->getRanking();
        return $this->render('JiliFrontendBundle:DecemberActivity:stat.html.twig');
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
                $logger->debug('{jarod}'. implode(':', array(__LINE__, __FILE__)));
                $session->set('goToUrl', $this->get('router')->generate('jili_frontend_decemberactivity_index'));
                return $this->redirect($this->generateUrl('_login') );
            }

            $form->bind($request);
            if( $form->isValid()){
                $data= $form->getData();

                $em  = $this->get('doctrine.orm.entity_manager');
                $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
                    ->insertUserPost( array('userId'=>$session->get('uid'),
                        'orderAt'=>$data['orderAt'], 
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

        $response->setData( array('code'=> 0, 
            'data'=>array('token'=> $record->getToken(),
            'numOfEggs'=> $record->getNumOfCommon(),
            'numOfConsolationEggs' => $record->getNumOfConsolation(),
            'lessForNextEgg'=> $record->getLessForNextEgg( )
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
