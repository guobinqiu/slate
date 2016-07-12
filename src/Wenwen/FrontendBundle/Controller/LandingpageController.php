<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class LandingpageController extends Controller
{
    /**
     * @Route("/landingpage", name="landingpage")
     * @Template
     */
    public function landingpageAction()
    {
        return $this->redirect($this->generateUrl('_homepage' ));
    }
}
