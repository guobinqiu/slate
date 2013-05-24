<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Jili\ApiBundle\Mailer;
use Jili\ApiBundle\Form\RegType;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="_default_index")
	 */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserment();
		$arr['advertise'] = $repository->getAdvertiserList();
		$callboard = $em->getRepository('JiliApiBundle:Callboard')->findAll();
		$arr['callboard'] =  $callboard;
    	foreach ($advertise as $k=>$v){
    		$i = 0;
    		if($v['type']==0){
    			$arr['advertise_banner'][] = $v;
    		}
    	}   
        return $this->render('JiliApiBundle:Default:index.html.twig',$arr);
    }
    
    /**
     * @Route("/about", name="_default_about")
     */
    public function aboutAction()
    {
    	return $this->render('JiliApiBundle:Default:about.html.twig');
    }
    
    /**
     * @Route("/help", name="_default_help")
     */
    public function helpAction()
    {
    	return $this->render('JiliApiBundle:Default:help.html.twig');
    }
     
   
    
}
