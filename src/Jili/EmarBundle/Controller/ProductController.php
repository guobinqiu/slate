<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Form\Type\SearchProductType;
use Jili\EmarBundle\Form\Type\SearchGeneralType;

use Jili\EmarBundle\Entity\EmarWebsites;

use Jili\EmarBundle\Api2\Repository\ItemCat as ItemCatRepository,
  Jili\EmarBundle\Api2\Repository\WebCat as WebCatRepository,
  Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

/**
 * @Route("/product")
 */
class ProductController extends Controller
{

    /**
     * @Route("/retrieve")
     * @Template();
     */
    public function retrieveAction( ) {
        if(!  $this->get('request')->getSession()->get('uid') ) {
            return  $this->redirect($this->generateUrl('_user_login'));
        }
        // cats & sub cats
        $request = $this->get('request');
        $logger = $this->get('logger');


        $cat_id = $request->query->getInt('cat');
        $web_id = $request->query->getInt('w');
        $price_range = $request->query->get('pr');
        $page_no = $request->query->get('p', 1);

        $prod_categories = $this->get('product.categories')->fetch();
        // websites:
        $filters_of_webs = $this->get('product.filters')->fetchWebs();
        $crumbs_local = ItemCatRepository::getCrumbsByScatid( $prod_categories['sub_cats'], $cat_id);

        if ( !empty($cat_id) || !empty($web_id) ) {
            $params = array( 'webid'=> $web_id, 'catid'=>$cat_id ,'page_no'=>$page_no, 'price_range'=> $price_range);
            $productRequest = $this->get('product.list_get');
            $products = $productRequest->fetch( $params);
            $total = $productRequest->getTotal();
        } else {
            $products = array();
            $total = 0;
        }

        return array_merge($prod_categories, $filters_of_webs,  compact('products', 'total','crumbs_local') );
    }

    /**
     * @Route("/category")
     * @Template();
     *
     *   $prod_categories = array('cats'=> array() , 'sub_cats'=> array());
     */
    public function categoryAction( ) {
        $prod_categories = $this->get('product.categories')->fetch();
        return $prod_categories;
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
     * @Route("/search/form")
     * @Template();
     */
    public function searchFormAction( $all = array()  ) {
        $request = $this->get('request');
        $logger= $this->get('logger');
        $form = $this->createForm(new SearchGeneralType());
        if( $request->isMethod('POST')) {
            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = ( isset( $query_params['q']) ) ?  $query_params['q'] : null ;
                $query = array_merge( $request->query->all(), $query_params );
                $url = $this->generateUrl('jili_emar_product_search') . '?' .http_build_query($query ) ;
                return $this->redirect($url );
            }
        }
        return   array('form'=> $form->createView() ,'all'=>$all) ;
    }

    /**
     * @Route("/search")
     * @Template();
     */
    public function searchAction() {
        if(!  $this->get('request')->getSession()->get('uid') ) {
            return  $this->redirect($this->generateUrl('_user_login'));
        }

        $request = $this->get('request');
        $logger= $this->get('logger');


        if( $request->isMethod('POST')) {
            $form = $this->createForm(new SearchGeneralType() );
            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = ( isset( $query_params['q']) ) ?  $query_params['q'] : null ;
            } else {
                $logger->error('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( 'form invalid', true) );
            }
            // try to update the request url query string 
            $query = array_merge( $request->query->all(), $query_params );
            //todo:redirect accroding to the rt param. 
            $url = $this->generateUrl('jili_emar_product_search') . '?' .http_build_query($query ) ;
            return $this->redirect( $url );
        } else {
            $keyword = $request->query->get('q');
            $form = $this->createForm(new SearchGeneralType(), array('q'=> $keyword, 'rt'=> 0 ) ); 
        }

        if( !isset($keyword ) || 0 >= strlen(trim($keyword))) {
            $url = $this->generateUrl('jili_emar_product_retrieve') ;
            if(  $request->query->count() > 0 ) {
                $url .= '?'.http_build_query($request->query->all() );
            }
            return $this->redirect( $url );
        } 
        $form = $this->createForm(new SearchGeneralType() , array('q'=> $keyword) );

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
        $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $params, true));
        $productSearch = $this->get('product.search');

        $page_size = $this->container->getParameter('emar_com.page_size_of_search') ;
        $productSearch->setPageSize( $page_size);
        $products = $productSearch->fetch($params );

        //分页时，只取有限数量。 

        $total = $productSearch->getTotal();
        
        // todo: cache stuff.
        $cache_id = md5(serialize($params));
        $cache_ts = time();


// fetch web_ids from products.
        
        // websites:
        // 设成100是为了取出筛选商家。
       // 1800 ?   
        #$productSearch->setPageSize( 100 );
        #$products = $productSearch->fetchForWebsiteFilter($params );

        $products_webids = array_filter(array_unique( array_map( function($v) { if ( isset($v['web_id'])) { return  $v['web_id']; } ; } , $products )));
//todo: parse the webs
        $filters_of_webs = $this->get('product.filters')->fetchWebsByParams( array( 'wids'=> $products_webids)  );

        $filters_of_webs = $this->get('product.filters')->fetchWebs(  );

        $filters_of_webs_d = $this->getDoctrine()->getManager()->getRepository('JiliEmarBundle:EmarWebsites')->getFilterWebs(  );
        $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $filters_of_webs_d, true));


        return   array_merge( $prod_categories,$filters_of_webs, array('search_form'=> $form->createView(),'products' => $products,'total'=> $total, 'crumbs_local'=> $crumbs_local/* , 'webs' => */));

    }

    
}
