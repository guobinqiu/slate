<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
#use Symfony\Component\HttpFoundation\Cookie;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;



use Jili\EmarBundle\Api2\Repository\ItemCat as ItemCatRepository,
  Jili\EmarBundle\Api2\Repository\WebCat as WebCatRepository,
  Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

/**
 * @Route("/product")
 */
class ProductController extends Controller
{
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

        // cats
        $categories_raw  = $this->get('general.category_get')->fetch();
        $cats = ItemCatRepository::parse( $categories_raw);

        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($categories_raw, true) );

        // wcats
        $wcategories_raw  = $this->get('website.category_get')->fetch( );
        $wcats = WebCatRepository::parse( $wcategories_raw);

        //webs
        $web_raw  = $this->get('website.list_get')->fetch( );
        #$webs = WebListRepository::parse( $web_raw);
        $webs = WebListRepository::parseByCat( $web_raw);
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($wcats, true) );
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($web_raw, true) );

       // todo: 
       //  $wcat = $request->request->get('wcat');
        $cat_id = $request->query->get('cat');
        $web_id = $request->query->get('w');

        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($web_ind, true) );

        #$cat_id = ItemCatRepository::fetchIdByIndex($cat_ind, $categories_raw );
        #$web_id = WebListRepository::fetchIdByIndex($web_ind, $web_raw );
        
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($web_raw, true) );
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($web_id, true) );

        $price_range = $request->query->get('pr');

        $page_no = $request->query->get('p', 1);
        //Deprecated: $order = $request->request->get('o'); 

        #$cat_id =  '101000000';
        #$web_id = '3414';
        if ( !empty($cat_id) || !empty($web_id) ) {
            // code...
            $params = array( 'webid'=> $web_id, 'catid'=>$cat_id ,'page_no'=>$page_no, 'price_range'=> $price_range);
            $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($params, true) );
            $productRequest = $this->get('product.list_get');
            $products = $productRequest->fetch( $params);
            $total = $productRequest->getTotal();
        } else {
            $products = array();
            $total = 0;
        }

        $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($total, true) );
        #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($web_id, true) );
    
        return array('products'=> $products,'cats'=>$cats, 'wcats'=> $wcats,'webs'=>$webs , 'total'=>$total );
    }

    /**
     * @Route("/filters")
     * @Template();
     */
    public function filtersAction(  ) {

        if(!  $this->get('request')->getSession()->get('uid') ) {
            return  $this->redirect($this->generateUrl('_user_login'));
        }

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
}
