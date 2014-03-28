<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Api2\Repository\GhsCat as GhsCatRepository;

/**
 * @Route("/ghs")
 */
class GhsController extends Controller
{

    /**
     * @Route("/filters")
     * @Template();
     */
    public function filtersAction() {
        $request = $this->get('request');
        $logger= $this->get('logger');

        #$req = new  \Jili\EmarBundle\Api2\Request\GhsCategoryGetRequest;

        $categoryRequest =  $this->get('ghs.category_get');
        $categories_raw  = $categoryRequest->fetch();
        $cats = GhsCatRepository::parse( $categories_raw);
        return array('cats'=> $cats);
    }

    /**
     * @Route("/partial-on-cps/{catids}", defaults={"catids"= ""} )
     * @Template();
     */
    public function partialOnCpsAction($catids) {

        $request = $this->get('request');
        $logger= $this->get('logger');

        //todo: added to cache file.
        $categoryRequest =  $this->get('ghs.category_get');
        $categories_raw  = $categoryRequest->fetch();
        $cats = GhsCatRepository::parse( $categories_raw);

        if( empty($catids) ) {
            $catids = implode(',', array_keys( $cats) );
        }

        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export( $catids, true )  );
        $listRequest = $this->get('ghs.list_get');
        $params =array('category'=> $catids);
        $list = $listRequest->fetch( $params );
        return array('router_'=> 'jili_emar_top_cps', 'catids_request'=>explode(',',$catids), 'cats'=> $catids, 'ghs_pdts'=> $list );
    }
}
