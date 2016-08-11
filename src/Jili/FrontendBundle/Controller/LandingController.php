<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/")
 */
class LandingController extends Controller
{
   /**
    * @Route("/lp/",name="_lp_page", methods={"GET"})
    */
   public function pageAction()
   {
       return  $this->redirect($this->generateUrl('_user_login'));
   }
}