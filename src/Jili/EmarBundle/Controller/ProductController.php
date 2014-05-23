<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Form\Type\SearchGeneralType;

use Jili\EmarBundle\Entity\EmarWebsites;

use Jili\EmarBundle\Api2\Repository\ItemCat as ItemCatRepository,
  Jili\EmarBundle\Api2\Repository\WebCat as WebCatRepository,
  Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

/**
 * @Route("/product", requirements={"_scheme" = "http"})
 */
class ProductController extends Controller
{
    /**
     * @Route("/retrieve")
     * @Method("GET")
     * @Template
     */
    public function retrieveAction() {
        // cats & sub cats
        $request = $this->get('request');
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();


        $cat_id =  $request->query->getInt('cat');
        $web_id =  $request->query->getInt('w');
        $price_range = $request->query->get('pr');
        $page_no = $request->query->getInt('p', 1);

        $prod_categories = $this->get('product.categories')->fetch();

        // websites:
        $webs = $this->get('product.filters')->fetchWebs();

        // filter 1:
        $filters_of_webs = $this->get('product.filters')->fetchWebsConfigged();

        // filter 2:
        if(isset($cat_id) && is_numeric($cat_id)) {
            $params=  array( 'categoryId'=>$cat_id);
            $webs_by_cat = $em->getRepository('JiliEmarBundle:EmarWebsitesCategory')->findBy($params );
            $wids_by_cat =  array();
            foreach($webs_by_cat as $row ) {
                $wid = $row->getWebId();
                if( ! array_key_exists( $wid, $filters_of_webs['webs'] )) {
                    $wids_by_cat[] = $wid ;
                }
            }
            $filters_of_webs_by_cat = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->fetchByWebIds($wids_by_cat );
            foreach($filters_of_webs_by_cat as $row) {
                $filters_of_webs['webs'][ $row->getWebId() ] =  $row;
            }
        }

            $logger = $this->get('logger');

        if ( !empty($cat_id) || !empty($web_id) ) {
            $params = array( 'webid'=> $web_id, 'catid'=> $cat_id ,'page_no'=>$page_no, 'price_range'=> $price_range);
            $logger->debug('{jarod}'. implode( ':', array(__LINE__, __CLASS__,'params','') ).var_export($params, true) );
            $productRequest = $this->get('product.list_get');
            $products = $productRequest->fetch( $params);
            $total = $productRequest->getTotal();
        } else {
            $logger->debug('{jarod}'. implode( ':', array(__LINE__, __CLASS__,'cat_id','') ).var_export($cat_id, true) );
            $logger->debug('{jarod}'. implode( ':', array(__LINE__, __CLASS__,'web_id','') ).var_export($web_id, true) );
            $products = array();
            $total = 0;
        }

        //update the commissions
        $webids=array();
        $web_commissions = array();

        foreach($products as $index => $product) {
            if( !empty(  $products [$index]['web_id'])) {
                $webids[] = $products [$index]['web_id'];
            }
        }
        array_unique($webids);

        $webs_configged = $em->getRepository('JiliEmarBundle:EmarWebsites')->getSortedByParams( array('wids'=> $webids ));
        $webids_configed = array();
        foreach( $webs_configged as $index => $row) {
            $web_configed[$row->getWebId()] = $row;
        }

        $result = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->fetchByWebIds( $webids );
        foreach( $result as $row) {
            $web_id = $row->getWebId();
            $web_croned[$web_id  ] = $row; 
        }

        foreach($webids as $webid ) {

            if( !isset( $web_croned[$webid ])){
                $logger->crit(implode( ':', array(__CLASS__ , __LINE__,'webid','')) .var_export($webid, true). ' not found in emar_website_croned table' );
                $web_commissions[$webid  ] = null;
            } else {
                $comm = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->parseMaxComission( $web_croned[$webid ]->getCommission()  );
                if( isset($web_configed[$webid] ) ) {
                    $multiple = $web_configed[$webid]->getCommission();
                } 
                if(! isset($multiple) ||  $multiple  === '' || $multiple === 0 || is_null( $multiple) ) {
                    $multiple= $this->container->getParameter('emar_com.cps.action.default_rebate');
                }
                $web_commissions[$webid  ] = round( $multiple * $comm/100, 2);
            }
        }
        $crumbs_local = ItemCatRepository::getCrumbsByScatid( $prod_categories['sub_cats'], $cat_id);

        return array_merge( $prod_categories, $webs, array('webs_filter'=> $filters_of_webs['webs'] ,'web_commissions'=>$web_commissions),compact('products', 'total','crumbs_local') );
    }

    /**
     * @param:  $prod_categories = array('cats'=> array() , 'sub_cats'=> array());
     * @Route("/category" )
     * @Template 
     */
    public function categoryAction( $qs = array(), $rt = null ) {
        $logger= $this->get('logger');

        $prod_categories = $this->get('product.categories')->fetch();
        $menu_config = $this->container->getParameter('emar_com.pdt_cat.menu');
        $cats_fliped = array_flip($prod_categories['cats']);

        // support 2-level only
        foreach( $menu_config as $index => $item) {
            if( is_array( $item) ) {

                foreach( $item as $key1 => $item1) {

                    foreach($item1 as $index2 => $item2) { // $item1 is always is array

                        if( $item2 === '图书音像') {
                            continue;
                        }
                        if(is_string( $item2 ) &&  array_key_exists($item2, $cats_fliped ))  {
                            $menu_config[$index][$key1][$index2] = array('cat_name'=> $item2, 'cat_id'=> $cats_fliped[$item2]); 
                        }
                    }
                }
            } else if(is_string( $item ) &&  array_key_exists($item, $cats_fliped ))  {
               $menu_config[$index] = array( 'cat_name'=> $item, 'cat_id'=> $cats_fliped[$item ]); 
            }
        }
        return array_merge($prod_categories , compact('rt', 'qs' ,'menu_config'));
    }


    /**
     * @Route("/recommend")
     * @Template();
     */
    public function recommendAction() {
        $response = $this->render('JiliEmarBundle:Product:recommend.html.twig');
        return array();
    }

    /**
     * @Route("/recommendByWeb/{wid}")
     * @Template();
     */
    public function recommendByWebAction($wid) {
        $request = $this->get('request');
        $logger= $this->get('logger');
        $params = array( 'webid'=> $wid,'page_no'=>1);
        $productRequest = $this->get('product.list_get');
        $productRequest->setPageSize(12); 
        $products = $productRequest->fetch( $params);
        return compact(/*'total',*/'products');
    }

    /**
     * @Route("/search")
     * @Method("GET")
     * @Template()
     */
    public function searchAction() {
        $request = $this->get('request');

        $em=$this->getDoctrine()->getManager();
        $logger= $this->get('logger');
        $keyword = $request->query->get('q');
        if( !isset($keyword ) || 0 >= strlen(trim($keyword))) {
            $url = $this->generateUrl('jili_emar_product_retrieve') ;
            if(  $request->query->count() > 0 ) {
                $url .= '?'.http_build_query($request->query->all() );
            }
            return $this->redirect( $url );
        } 

        $cat_id = $request->query->getInt('cat');
        $web_id = $request->query->getInt('w');
        $price_range = $request->query->get('pr');
        $order = $request->query->get('o',1);
        $page_no = $request->query->get('p', 1);

        // catetory 
        $prod_categories = $this->get('product.categories')->fetch();
        $crumbs_local = ItemCatRepository::getCrumbsByScatid( $prod_categories['sub_cats'], $cat_id);
        
        // search
        $params = array('keyword'=>$keyword, 'catid'=> $cat_id, 'webid'=> $web_id, 'page_no'=>$page_no, 'price_range'=> $price_range,'orderby'=>$order);
        $productSearch = $this->get('product.search');

        $page_size = $this->container->getParameter('emar_com.page_size_of_search') ;
        $productSearch->setPageSize( $page_size);
        $products = $productSearch->fetch($params );

        //分页时，只取有限数量。 
        $total = $productSearch->getTotal();
        
        // fetch web_ids from products.
        // websites:
        // 设成100是为了取出筛选商家。
       // 1800 ?   

        $products_webids = array_filter(array_unique( array_map( function($v) { if ( isset($v['web_id'])) { return  $v['web_id']; } ; } , $products )));

        array_unique($products_webids);

//todo: parse the webs
        $webs = $this->get('product.filters')->fetchWebs();
        $filters_of_webs = $this->get('product.filters')->fetchWebsConfigged();
        // get commissions
        $web_commissions = array();
        $result = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->fetchByWebIds( $products_webids );

        foreach( $result as $row) {
            $webid = $row->getWebId();
            $comm = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->parseMaxComission( $row->getCommission()  );
            $multiple= $this->container->getParameter('emar_com.cps.action.default_rebate');
            $web_commissions[$webid  ] = round( $multiple * $comm/100, 2);
        }


        //$filters_of_webs_d = $this->getDoctrine()->getManager()->getRepository('JiliEmarBundle:EmarWebsites')->getFilterWebs(  );
        return   array_merge( $prod_categories, $webs ,  array('products' => $products,'total'=> $total, 'crumbs_local'=> $crumbs_local/* , 'webs' => */, 'webs_filter'=>$filters_of_webs,'web_commissions'=>$web_commissions));
    }

    
}
