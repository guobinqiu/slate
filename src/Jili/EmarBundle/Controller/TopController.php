<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/top")
 */
class TopController extends Controller
{
    /**
     * @Route("/cps")
     * @Template();
     */
    public function cpsAction()
    {

        //search form 
        // hot activity
        // hot websites from configed
         
        $prod_categories = $this->get('product.categories')->fetch();
        return $prod_categories;
    }

}
