<?php
namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/",requirements={"_scheme"="http"})
 */
class LandingController extends Controller implements CampaignTrackingController
{

    /**
     * @Route("/lp/",name="_lp_page" )
     * @Method({ "GET"})
     * @Template
     */
   public function pageAction()
   {
       return  $this->redirect($this->generateUrl('_user_login'));
   }
}
