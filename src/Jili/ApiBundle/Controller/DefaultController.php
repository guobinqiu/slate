<?php
namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();

    	$query = $em->getRepository('JiliApiBundle:Advertiserment');

    	$advertise = $query->findAll();
        $arr['advertise'] = $advertise;
        return $this->render('JiliApiBundle:Default:index.html.twig',$arr);
    }
}
