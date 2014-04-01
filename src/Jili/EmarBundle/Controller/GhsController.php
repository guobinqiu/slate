<?php
namespace Jili\EmarBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\EmarBundle\Api2\Repository\GhsCat as GhsCatRepository;

/**
 * @Route("/ghs")
 */
class GhsController extends Controller
{
    /**
     * tmpl: the template to embed, max: number of records, p: the page
     * @Route("/promotion/{tmpl}/{max}/{p}", defaults={"tmpl"="top", "max"=6 ,"p"=1}, requirements={"tmpl"="\w+", "max"="\d+", "p"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function promotionAction($tmpl, $max, $p) {
        $request = $this->get('request');
        $logger = $this->get('logger');

        $listRequest = $this->get('ghs.list_get');
        $listRequest->setPageSize($max);
        $params = array('page_no' => $p);

        $list = $listRequest->setApp('cron')->fetch( $params );


        if( $request->isXmlHttpRequest()) {

            $response = new Response(json_encode(array('name' => $name)));
            $response->headers->set('Content-Type', 'application/json');

        }
        return array();
    }

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
        $listRequest = $this->get('ghs.list_get');
        $listRequest->setPageSize( $this->container->getParameter('emar_com.page_size_of_topcps') );
        $list = $listRequest->setApp('cron')->fetch( $params );

        return array('router_'=> 'jili_emar_top_cps', 'catids_request'=> $catids_request, 'cats'=> $cats, 'ghs_pdts'=> $list );
    }
}
