<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
#use Symfony\Component\HttpFoundation\Cookie;

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

                $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $query_params, true) );
                $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $request->query->all() , true) );

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
            $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $params, true));

            $productRequest = $this->get('product.list_get');

            $products = $productRequest->fetch( $params);

            $total = $productRequest->getTotal();

        } else {
            $products = array();
            $total = 0;
        }

        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'','produts','')) . var_export( $products , true));
        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'','total','')) . var_export( $total, true));

        return array_merge($filters, array('products'=> $products,  'total'=>$total ));
    }

    /**
     * @Route("/filters")
     * @Template();
     */
    public function filtersAction(  ) {

       # if( !  $this->get('request')->getSession()->get('uid') ) {
       #     return  $this->redirect($this->generateUrl('_user_login'));
       # }

        $request = $this->get('request');
        $logger= $this->get('logger');

        $cat = $request->request->get('cat');
        $wcat = $request->request->get('wcat');
        $w = $request->request->get('w');

        #    $websiteRequest =  $this->get('general.website_get');
        #    $websites_raw = $websiteRequest->fetch();
        #    $webs = HotWebRepository::parse( $websites_raw);

        $categories_raw  = $this->get('general.category_get')->fetch( );
        $cats = ItemCatRepository::parse( $categories_raw);

        // wcats
        $wcategories_raw  = $this->get('website.category_get')->fetch( );
        $wcats = WebCatRepository::parse( $wcategories_raw);

        //webs
        $web_raw  = $this->get('website.list_get')->fetch( );
        $webs = WebListRepository::parse( $web_raw);

        # $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $web_raw, true));

        $template=$this->get('templating');

        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $route, true));
        #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $qs, true));

        $content = $template->render('JiliEmarBundle:Product:filters.html.twig',  array('cats'=>$cats, 'wcats'=> $wcats,'webs'=> $webs/* ,'route' => $route, 'qs'=>$qs */));

        $response = new Response($content);

        #$cookie = new Cookie('user', 'jarod', 0, '/', null, false, false); //last argument
        #$response->headers->setCookie( $cookie);
        return $response;
    }




    /**
     * @Route("/orderby")
     * @Template();
     */
    public function orderbyAction(  ) {

        return array();
    }
}
