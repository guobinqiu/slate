<?php

namespace Jili\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
/**
 * @Route("/auto_checkin")
 */
class AutoCheckinController extends Controller
{
    /**
     * Ajax to save auto checkin log 
     * @Route("/save_data")
     * @Template()
     */
    public function saveDataAction()
    {
        return array();
    }

    /**
     * Ajax to hand out the checkin points.
     * @Route("/add_bonus")
     * @Template()
     */
    public function addBonusAction()
    {
        return array();
    }

    /**
     * render the shop list for checkin.
     *
     * @Route("/list")
     * @Template()
     * @Method("GET")
     */
    public function listAction()
    {

        return array();
    }

    /**
     * to convert the auto checkin to manual checkin 
     * @Route("/update")
     * @Template()
     *
     */
    public function update()
    {
        return array();
    }

}
