<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/top")
 */
class TopController extends Controller
{
    /**
     * @Route("/cps")
     * @Method("GET");
     * @Template();
     */
    public function cpsAction()
    {
        return array();
    }

}
