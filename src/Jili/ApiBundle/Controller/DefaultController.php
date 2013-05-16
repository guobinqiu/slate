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
	 * @Route("", name="_default_index")
	 */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('JiliApiBundle:Advertiserment');
		$advertise = $repository->getAdvertiserment();
// 		$callboard = $em->getRepository('JiliApiBundle:Callboard')->findAll();
// 		$arr['callboard'] =  $callboard;
    	foreach ($advertise as $k=>$v){
    		if($v['type']==0){
    			$arr['advertise_banner'][] = $v;
    		}
    		if($v['type']==1){
    			$arr['advertise'][] = $v;
    		}
    	}   
        return $this->render('JiliApiBundle:Default:index.html.twig',$arr);
    }
   
    
}
