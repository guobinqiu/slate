<?php

namespace Jili\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller implements  IpAuthenticatedController
{
    public function indexAction($name)
    {
        return $this->render('JiliBackendBundle:Default:index.html.twig', array('name' => $name));
    }
}
