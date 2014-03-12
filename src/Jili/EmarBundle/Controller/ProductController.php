<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
#use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Filesystem\Filesystem;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;



use Jili\EmarBundle\Form\Type\SearchProductType;

use Jili\EmarBundle\Api2\Repository\ItemCat as ItemCatRepository,
  Jili\EmarBundle\Api2\Repository\WebCat as WebCatRepository,
  Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

/**
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/search")
     * @Template();
     */
    public function searchAction() {
        $request = $this->get('request');
        $logger= $this->get('logger');
        $form = $this->createForm(new SearchProductType() );
        if( $request->isMethod('POST')) {
            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();

              #  $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $request->query->all() , true) );

                $keyword = $query_params['keyword'];
                #$qs = 0 < count($query_params) ? 


                #TODO: merge query with request parameters.
                #      add the catid , webid, price_range, orderby
                $url = $this->generateUrl('jili_emar_product_result') .'?'. http_build_query( array('q'=> $keyword ) ) ;
                return $this->redirect( $url );
            }
        }
        $filters = $this->get('product.filters')->fetch( );
        return   array('form'=> $form->createView(), 'filters' => $filters);
    }

    /**
     * @Route("/result")
     * @Template();
     */
    public function resultAction() {

        $request = $this->get('request');
        $logger= $this->get('logger');


        $keyword = $request->query->get('q');
        $page_no = $request->query->get('p', 1);
        $order = $request->query->get('o',1);
        $price_range = $request->query->get('pr', '');

        $catid = $request->query->get('c');
        $webid = $request->query->get('w');

        $params_form = array('keyword'=>$keyword, 'catid'=> $catid, 'webid'=> $webid) ;

        $form = $this->createForm(new SearchProductType(), $params_form );

        $params = array('keyword'=>$keyword, 'catid'=> $catid, 'webid'=> $webid, 'page_no'=>$page_no, 'price_range'=> $price_range,'orderby'=>$order);

        $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $params, true));

        $productSearch = $this->get('product.search');

        $products = $productSearch->fetch($params );

        $total = $productSearch->getTotal();

        $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $total, true));
        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $products , true));

        $filters = $this->get('product.filters')->fetch( );
        return   array('form'=> $form->createView(), 'filters' => $filters, 'products' => $products,'total'=> $total);
    }

    /**
     * @Route("/list")
     * @Template();
     */
    public function listAction( ) {
        if(!  $this->get('request')->getSession()->get('uid') ) {
            return  $this->redirect($this->generateUrl('_user_login'));
        }

        $request = $this->get('request');
        $logger= $this->get('logger');

        $filters = $this->get('product.filters')->fetch( );

        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $filters , true));

        // todo: 
        //  $wcat = $request->request->get('wcat');
        
        $cat_id = $request->query->get('cat');
        $web_id = $request->query->get('w');

        $price_range = $request->query->get('pr');
        $page_no = $request->query->get('p', 1);

        if ( !empty($cat_id) || !empty($web_id) ) {
            $params = array( 'webid'=> $web_id, 'catid'=>$cat_id ,'page_no'=>$page_no, 'price_range'=> $price_range);
            #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $params, true));

            $productRequest = $this->get('product.list_get');
            $products = $productRequest->fetch( $params);
            $total = $productRequest->getTotal();

        } else {
            $products = array();
            $total = 0;
        }

        return array_merge($filters, array('products'=> $products,  'total'=>$total ));
    }
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

        $prod_categories = $this->get('product.categories')->fetch();

        // websites:
        $filters_of_webs = $this->get('product.filters')->fetchWebs();


        $cat_id = $request->query->getInt('cat');
        $web_id = $request->query->getInt('w');

        $price_range = $request->query->get('pr');
        $page_no = $request->query->get('p', 1);

        $crumbs_local = ItemCatRepository::getCrumbsByScatid( $prod_categories['sub_cats'], $cat_id);
        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $crumbs_local, true));

        if ( !empty($cat_id) || !empty($web_id) ) {
            $params = array( 'webid'=> $web_id, 'catid'=>$cat_id ,'page_no'=>$page_no, 'price_range'=> $price_range);

            $productRequest = $this->get('product.list_get');

            $products = $productRequest->fetch( $params);


            #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $product_webs, true));

            $total = $productRequest->getTotal();

        } else {
            $products = array();
            $total = 0;
        }

        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $products, true));
        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $total, true));

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
        #$response = $this->render('JiliEmarBundle:Product:category.html.twig', $prod_categories);
        #$response->setSharedMaxAge(86400);
        #return $response;
    }


    /**
     * @Route("/recommend")
     * @Template();
     */
    public function recommendAction() {
        $response = $this->render('JiliEmarBundle:Product:recommend.html.twig');
        return array();
        #$response->setSharedMaxAge(86400);
        #return $response;

    }
}
        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'','produts','')) . var_export( $products , true));
        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'','total','')) . var_export( $total, true));
