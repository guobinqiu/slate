<?php

namespace Wenwen\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class eventController extends Controller
{
    /**
     * @Route("/invitation", name="invitation")
     */
    public function invitationAction()
    {
        return $this->render('WenwenFrontendBundle:Event:invitation.html.twig');
    }
}