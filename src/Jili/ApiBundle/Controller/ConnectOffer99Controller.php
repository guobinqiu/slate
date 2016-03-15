<?php
namespace Jili\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

class ConnectOffer99Controller extends Controller
{

    /**
     * @Route("/landing", name="_default_landing",requirements={"_scheme"="http"})
     */
    public function landingAction()
    {
        $request = $this->get('request');
        $validator = $this->get('validator');

        // validtor

        // query inviteee

        // return json result
        return new Response(__FILE__);
    }
}
