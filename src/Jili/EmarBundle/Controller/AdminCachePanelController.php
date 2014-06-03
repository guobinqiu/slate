<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\BackendBundle\Controller\IpAuthenticatedController;

/**
 * @Route("/admin/cachepanel",requirements={"_scheme"="https"})
 */
class AdminCachePanelController extends Controller implements  IpAuthenticatedController
{
    /**
     * @Route("/")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $cache_dir=$this->getParameter('cache_data_path');
        $all = disk_total_space($cache_dir);
        $all = $all / 1024; // bytes => 1B   1KB  1MB
        $all = round( $all, 2);

        // list some cache info
        // all cache size
        return array(); 
    }

    /**
     * @Route("/remove")
     * @Method("DELETE")
     * @Template
     */
    public function doRemoveAction() 
    {

        
        return array(); 
    }
}
