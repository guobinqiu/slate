<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Jili\EmarBundle\Form\Type\WebsiteFilterType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/advertisement/shop", requirements={"_scheme" = "http"})
 */
class AdvertisementWebsiteController extends Controller
{
    // redirect
    // detail
    // list
    // search
   
    /**
     * @Route("/detail/{wid}", requirements={"wid" = "\d+"}, defaults={"wid" = 0})
     * todo: added pageno for recommend
     */
    public function detailAction(Request $request)
    {
        return new Resposne(__FUNCTION__); 
    }

    /**
     * @abstract: 会将本地配置的商家排列在前面。
     * @Route("/list")
     * @Method( {"GET","POST"})
     */
    public function listAction(Request $request)
    {
        return new Resposne(__FUNCTION__); 
    }

}

