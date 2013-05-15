<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class  CallboardController extends Controller
{
	/**
	 * @Route("/", name="_callboard_index")
	 */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
		$callboard = $em->getRepository('JiliApiBundle:Callboard')->findAll();
        $arr['callboard'] =  $callboard;   	
        return $this->render('JiliApiBundle:Callboard:index.html.twig',$arr);
    }
    
    /**
     * @Route("/info/{id}", name="_callboard_info")
     */
    public function infoAction($id){
    	$em = $this->getDoctrine()->getManager();
    	$callboard = $em->getRepository('JiliApiBundle:Callboard')->find($id);
    	$arr['callboard'] = $callboard;
    	return $this->render('JiliApiBundle:Callboard:info.html.twig',$arr);
    	
    }
   
    
}
