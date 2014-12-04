<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\FrontendBundle\Form\Type\GameEggsBreakerTaoBaoOrderType;

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
        // code...  
    }

    /**
     * render the ranking 
     * @Route("/stat")
     * @Method("GET")
     */
    public function statAction()
    {
        // code...
    }

    /**
     * @Route("/add-taobao-order")
     * @Method({"GET","POST"})
     */
    public function addTaobaoOrderAction()
    {
        $request = $this->get('request');
        $form = $this->createForm(new GameEggsBreakerTaoBaoOrderType());
        if( 'POST' == $request->getMethod()) {

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
        // code...
    }

    /**
     * @Route("/break-egg", options={"expose"=true})
     * @Method("POST")
     */
    public function breakEggAction()
    {
        // code...
    }
}
