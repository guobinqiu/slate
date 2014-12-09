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
            $form->bind($request);
            if( $form->isValid()){
                $data= $form->getData();

                $em  = $this->get('doctrine.orm.entity_manager');
                $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
                    ->insertUserPost( array('userId'=>$session->get('uid'),
                        'orderPaid'=>$data['orderPaid'], 
                        'orderId'=>$data['orderId'], 
                    ));
                // store the post data 
                $this->get('session')->setFlash('notice','提交成功，等待审核');
                return $this->rediret($this->generate('jili_frontend_decemberactivity_index'));
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
        $response = JsonResponse ();
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

        $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo');
        // completed response 
        //->

    }

    /**
     * @Route("/break-egg", options={"expose"=true})
     * @Method("POST")
     */
    public function breakEggAction()
    {
        // more stuff 
    }
}
