<?php
namespace Jili\EmarBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Jili\EmarBundle\Api2\Repository\GhsCat as GhsCatRepository;

/**
 * @Route("/ghs", requirements={"_scheme" = "http"})
 */
class GhsController extends Controller
{
    /**
     * @param: tmpl: the template to embed,
     * @param: max: number of records per page,
     * @param:  p: the page number
     * @Route("/promotion/{tmpl}/{max}/{p}", defaults={"tmpl"="top", "max"=6 ,"p"=1}, requirements={"tmpl"="search|top", "max"="\d+", "p"="\d+"})
     * @Method({ "GET", "POST"})
     * @Template
     */
    public function promotionAction($tmpl, $max, $p)
    {
        $request = $this->get('request');
        $logger = $this->get('logger');

        $api_name = 'ghs.list_get';
        $listRequest = $this->get($api_name);

        $session = $request->getSession() ;
        $last_page_session_key = $api_name.'.'.$tmpl.'.fetched';

        if( $session->has($last_page_session_key)) {
            $last_page =  $session->get($last_page_session_key);
        } else {
            $listRequest->setPageSize(1);
            $params = array('page_no' => 1);
            $listRequest->setApp('search')->fetchDistinct( $params );
            $total = $listRequest->getTotal();
            $last_page =ceil( $total/2/$max);
            $session->set($last_page_session_key, $last_page);
        }

        if( $p > $last_page) {
            $page = $p % $last_page;
        } else {
            if( $p > 0 ) {
                $page  = $p;
            } else {
                $page = mt_rand(1, $last_page);
            }
        }
        // multiple by 2 to filter the unecessary links.
        // NOTICE: always fetch the first page ?
        $listRequest->setPageSize($max );
        $params = array('page_no' => $page);
        $list = $listRequest->setApp('search')->fetchDistinct( $params );

        if( $request->isXmlHttpRequest()) {
            $prds = array();
            foreach( $list as $v) {
                $prds[] = array('pic'=> $v['pic_url'],
                    'href'=> $this->generateUrl('jili_emar_default_redirect').'?m='. urlencode( $v['ghs_o_url']) ,
                    'name'=> $v['p_name'],
                    'pri1'=> $v['ghs_price'],
                    'pri0'=>$v['ori_price'] ,
                    'dis'=> round( $v['discount'] *  $this->container->getParameter('emar_com.cps.action.default_rebate')/100, 2),
                    'buy'=>$v['bought'] );
            }

            $response = new Response(json_encode(array('prds' => $prds)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $template ='JiliEmarBundle:Ghs:'. 'promotion_on_'. $tmpl. '.html.twig';
        return $this->render($template, array('ghs_pdts'=> $list ,'page'=> $page,'last_page'=>$last_page));
    }

    /**
     * @abstract: only 1 page.
     * @Route("/partial/{tmpl}/{catids}", defaults={"tmpl"="cps", "catids"= ""}, requirements={"tmpl"="cps" } )
     * @Template()
     */
    public function partialAction($tmpl,$catids)
    {
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
        $listRequest->setPageSize(  $this->container->getParameter('emar_com.page_size_of_topcps') );

        $list = $listRequest->setApp('cron')->fetchDistinct( $params );

        $return =  array('router_'=> 'jili_emar_top_cps', 'catids_request'=> $catids_request, 'cats'=> $cats, 'ghs_pdts'=> $list );

        $template ='JiliEmarBundle:Ghs:partial_on_'. $tmpl. '.html.twig';
        return $this->render($template, $return );
    }
}
