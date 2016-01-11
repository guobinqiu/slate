<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

use Jili\FrontendBundle\Form\Type\SignupType;

/**
 * @Route("/",requirements={"_scheme"="http"})
 */
class LandingController extends Controller implements CampaignTrackingController
{

    /**
     * @Route("/lp",name="_lp_page" )
     * @Method({ "GET"})
     * @Template
     */
   public function pageAction() 
   {
       return $this->forward('JiliApiBundle:User:reg'); 
   }
}
