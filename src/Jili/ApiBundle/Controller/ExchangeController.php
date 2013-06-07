<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class  ExchangeController extends Controller
{
	/**
	 * @Route("/center", name="_exchange_index")
	 */
    public function indexAction()
    {
    	$arr['userId'] = 1;
        return $this->render('JiliApiBundle:Exchange:index.html.twig',$arr);
    }
    
    /**
     * @Route("/info/{id}", name="_exchange_info")
     */
    public function infoAction($id){
    	$em = $this->getDoctrine()->getManager();
    	$user = $em->getRepository('JiliApiBundle:User')->find($id);
    	$arr['user'] = $user;
    	return $this->render('JiliApiBundle:Exchange:info.html.twig',$arr);
    }
    
    /**
     * @Route("/finish/{id}", name="_exchange_finish")
     */
    public function finishAction($id){
    	$em = $this->getDoctrine()->getManager();
    	$user = $em->getRepository('JiliApiBundle:User')->find($id);
    	$arr['user'] = $user;
    	return $this->render('JiliApiBundle:Exchange:finish.html.twig',$arr);
    }
     
   
    
}
