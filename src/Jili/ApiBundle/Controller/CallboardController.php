<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class  CallboardController extends Controller
{
	/**
	 * @Route("/index", name="_callboard_index")
	 */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
		$callboard = $em->getRepository('JiliApiBundle:CallBoard')->getCallboard();
        $arr['callboard'] =  $callboard;   	
        $paginator  = $this->get('knp_paginator');
        $arr['pagination'] = $paginator->paginate(
        		$callboard,
        		$this->get('request')->query->get('page', 1),
        		$this->container->getParameter('page_num')
        );
        $arr['pagination']->setTemplate('JiliApiBundle::pagination.html.twig');
        return $this->render('JiliApiBundle:Callboard:index.html.twig',$arr);
    }
    
    /**
     * @Route("/info/{id}", name="_callboard_info")
     */
    public function infoAction($id){
    	$em = $this->getDoctrine()->getManager();
    	$callboard = $em->getRepository('JiliApiBundle:Callboard')->find($id);
        if($callboard)
    	    $arr['callboard'] = $callboard;
        else
            return $this->redirect($this->generateUrl('_default_error'));
    	return $this->render('JiliApiBundle:Callboard:info.html.twig',$arr);
    	
    }
   
    
}
