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
     * @Route("/partial-on-cps/{catids}", defaults={"catids"= ""} )
     * @Template()
     */
    public function partialOnCpsAction($catids) {

        $request = $this->get('request');
        $logger= $this->get('logger');

        //todo: added to cache file.
        $categoryRequest =  $this->get('ghs.category_get');
        $categories_raw  = $categoryRequest->fetch();
        $cats = GhsCatRepository::parse( $categories_raw);
        $cat_ids = array_keys( $cats);

        $catids_request = '000000'; // 全部
        if( ! empty($catids) ) {
            $catids_ = array_intersect($cat_ids, explode(',' , $catids)); 
            if( ! empty($catids_) ) {
                $catids_request = $catids_;
            }
        }

        $params =( $catids_request === '000000' ) ? array('category'=> '') : array('category'=> implode(',', $catids_));

        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export( $this->container->getParameter('emar_com.page_size_of_topcps'), true )  );
        $listRequest = $this->get('ghs.list_get');

        $listRequest->setPageSize( $this->container->getParameter('emar_com.page_size_of_topcps') );

        $list = $listRequest->setApp('cron')->fetch( $params );

        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export( count($list) , true )  );
        return array('router_'=> 'jili_emar_top_cps', 'catids_request'=> $catids_request, 'cats'=> $cats, 'ghs_pdts'=> $list );
    }
}
