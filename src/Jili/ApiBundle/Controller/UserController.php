<?php
namespace Jili\ApiBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\LoginLog;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
	/**
	 * @Route("/{id}", name="_user_index")
	 */
	public function indexAction($id)
	{
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('JiliApiBundle:User')->find($id);
		$arr['user'] = $user;
		return $this->render('JiliApiBundle:User:index.html.twig',$arr);
	}
	
	
	
    
    
}
