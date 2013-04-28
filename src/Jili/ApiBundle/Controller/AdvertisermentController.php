<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class AdvertisermentController extends Controller
{
	/**
	 * @Route("/{id}", name="_advertiserment_index")
	 */
	public function indexAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$advertiserment = $em->getRepository('JiliApiBundle:Advertiserment')->find($id);
		$arr['advertiserment'] = $advertiserment;
		return $this->render('JiliApiBundle:Advertiserment:index.html.twig',$arr);
	}
	
	
}
