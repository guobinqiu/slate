<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/default")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/index")
     */
    public function indexAction($name)
    {
//todo: display goods.

        return $this->render('JiliEmarBundle:Default:index.html.twig', array('name' => $name));
    }
}
