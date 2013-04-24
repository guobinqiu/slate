<?php

namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Jili\ApiBundle\Entity\Advertiserment;
use Jili;

class DefaultController extends Controller
{
    public function indexAction($name='hhhh')
    {
    	$advertiserment = $this->getDoctrine()->getRepository('JiliApiBundle:Advertiserment')->find($id);
    	if (!$advertiserment) {
    		throw $this->createNotFoundException('No advertiserment found for id' .$id);
    	}
    	$repository = $this->getDoctrine()->getRepository('JiliApiBundle:Advertiserment');
    	$advertiserment = $repository->find($id);
    	$advertiserments = $repository->findAll();
		echo $advertiserments;    	
        return $this->render('JiliApiBundle:Default:index.html.twig', array('name' => $name));
    }
}
