<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

use Jili\EmarBundle\Form\Type\WebsiteFilterType;
use Jili\EmarBundle\Form\Type\SearchWebsiteType;

use Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

/**
 * @Route("/websites")
 */
class WebsitesController extends Controller
{

    /**
     * @Route("/shoplist/search")
     * @Method("GET")
     * @Template()
     */
    public function shopListSearchAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');

        $wcat_id = $request->query->get('wcat' );

        $form = $this->createForm(new WebsiteFilterType() );
        if( $request->isMethod('post')) {
            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = $query_params['keyword'];
                $parameters = array('q'=> $keyword );
                if( isset($wcat_id ) && is_numeric($wcat_id) && $wcat_id > 0 ) {
                    $parameters ['wcat']= $wcat_id ;
                }
                $url = $this->generateUrl('jili_emar_websites_shoplist') .'?'. http_build_query( $parameters ) ;
                return $this->redirect( $url );
            }
        }
        return  array('form'=> $form->createView());
    }

    /**
     * @abstract: 会将本地配置的商家排列在前面。
     * @Route("/shoplist")
     * @Method("GET")
     * @Template()
     */
    public function shopListAction()
    {
        if(!  $this->get('request')->getSession()->get('uid') ) {
            return  $this->redirect($this->generateUrl('_user_login'));
        }

        $request = $this->get('request');
        $logger= $this->get('logger');
        $em = $this->getDoctrine()->getManager();

        $wcat_id = (int) $request->query->get('wcat', 0 );
        $keyword = $request->query->get('q', '');
        $page_no = $request->query->getInt('p',1);

        // wcats with local file cache
        $wcats = $this->get('website.categories')->fetch() ;
        // webs 
        $websites = array();
        $params =array();
        if( isset($wcat_id ) && is_numeric($wcat_id) && $wcat_id > 0 ) {
            $params = array('catid'=> $wcat_id );
        } 

        $web_raw  = $this->get('website.list_get')->fetch( $params );

        $fitlers = array();
        // for wcat_id = 0, the hot websites only.
        if( $wcat_id === 0 ) {
            $web_hot = $em->getRepository('JiliEmarBundle:EmarWebsites')->getHot( array('select'=> '*') );
            $web_hot_ids = array();
            foreach($web_hot as  $row) {
                $web_hot_ids[] = $row->getWebId();
            }
            if(count($web_hot_ids) > 0 ) {
                $fitlers['wids'] = $web_hot_ids;
            }
        } 

        // searching 
        if( strlen(trim($keyword)) > 0) {
            $web_searched = $this->get('website.search')->find( $web_raw, $keyword );
            $websites = WebListRepository::parse( $web_searched, $fitlers);
        } else {
            $websites = WebListRepository::parse( $web_raw, $fitlers);
        }

        $webids =  array_keys($websites);
        $params ['wids'] = $webids;

        //todo: use a index of  type  array (  wid=>index  );
        //todo: add catid into where
        if( $wcat_id === 0 ) {
            $websites_configed = $web_hot; 
        } else {
            $websites_configed = $em->getRepository('JiliEmarBundle:EmarWebsites')->getSortedByParams( $params );
        }

        /// sorting 
        $websites_filtered = array();
        $websites_left  = $websites;
        foreach($websites_configed as $row ) {
            if(in_array( $row->getWebId() , $webids )) {
                $websites_filtered[ $row->getWebId() ] =  $websites_left[ $row->getWebId() ];
                unset($websites_left[$row->getWebId()]);
            }
        }

        $websites_sorted = $websites_filtered + $websites_left; //array_diff($websites, $websites_filtered);

        // page_size , page_no 
        $total =  count($websites);
        $page_size = $this->container->getParameter('emar_com.page_size_of_shoplist');

        $i = 0;
        $start = ( $page_no -1 ) * $page_size ; 
        $end =  $start + $page_size;
        $websites_paged = array();

        // todo: use array_slice()
        // #$websites_paged = array_slice( $websites_sorted, ( $page_no -1 ) * $page_size  , $page_size );
        foreach($websites_sorted as $k => $v) {
            if(  $start <= $i ) {
                if( $i >= $end ) {
                    break;
                }
                $websites_paged[$k]  =$v;
            } 
            $i++;
        }

        // update the commission by configed in emar_websites 
        $websites_configed_wid = array();
        foreach($websites_configed as $row ) {
            $websites_configed_wid [$row->getWebId() ] = $row; 
        }

        foreach ($websites_paged as $k => $v) {
            $comm = 0;
            if( isset( $websites_configed_wid[$k] )) {
                $row = $websites_configed_wid[$k];
                if( 0 <  strlen(trim($row->getCommission()))) {
                    $comm = $row->getCommission();
                }
            } 
            if($comm === ''|| $comm === 0 || is_null($comm)) {
                $comm = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->parseMaxComission($websites_paged[$k] ['commission'] );
            }

            if($comm === '' || $comm === 0 || is_null($comm)) {
                $comm = $this->container->getParameter('emar_com.cps.action.default_rebate');
            }

            $websites_paged[$k] ['commission'] = $comm;
        }

        $filter_form = $this->createForm(new WebsiteFilterType(), array('keyword'=>$keyword)  );
        return  array('categories'=> $wcats, 'websites'=> $websites_paged, 'total'=> $total,'filter_form'=>$filter_form->createView() );
    }


    /**
     * 返利商城: 从emar_websits中取数据，
     * @Route("/hot/{tmpl}/{max}", defaults={"tmpl"="top", "max" = 12 }, requirements={"tmpl"= "\w+",  "max" = "\d+"} )
     * @Method("GET")
     * @Template()
     */
    public function hotAction($tmpl, $max)
    {
        //todo restrice the request ip ?
        $logger = $this->get('logger');
        $em = $this->getDoctrine()->getManager();

        $params = array();
        if(isset($max) && $max > 0 ) {
            $params['limit'] = $max;
        }

        // fetch the details ? 
        $hot_webs_configed = $em->getRepository('JiliEmarBundle:EmarWebsites')->getHot( $params);
        $websites = array();

        if(! empty($hot_webs_configed)) {
            $webids= array();
            $commissions = array();
            foreach($hot_webs_configed as $row) {
                $webids[] = $row[ 'webId']; 
                $commissions[ $row['webId'] ] = $row['commission'];
            }


            $result = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->fetchByWebIds( $webids );

            # input commissions_of_configed , $commission_of_api, $commission_of_default;
            foreach( $result as $row) {
                $web_id = $row->getWebId();
                $comm = 0;
                if( array_key_exists( $web_id,  $commissions) ){
                    $comm = $commissions[$web_id];
                } 

                if( $comm === '' || $comm === 0 || is_null( $comm) ) {
                    $comm = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->parseMaxComission($row->getCommission() );
                } 

                if( $comm === '' || $comm === 0 || is_null( $comm) ) {
                    $comm = $this->container->getParameter('emar_com.cps.action.default_rebate');
                }

                $row->setCommission($comm);
                $websites[$row->getWebId() ] = $row; 
            }
        }
        

        // update the commission if exists
        
        $template ='JiliEmarBundle:Websites:'. 'hot_on_'. $tmpl. '.html.twig';
        return $this->render($template, compact('websites'));
    }

    /**
     * @Route("/detail/{wid}", requirements={"wid" = "\d+"}, defaults={"wid" = 0})
     * @Template()
     * todo: added pageno for recommend
     */
    public function detailAction($wid )
    {
        $request = $this->get('request');
        if(!  $request->getSession()->get('uid') ) {
            return  $this->redirect($this->generateUrl('_user_login'));
        }
        $params = array('webid'=>$wid );
        $website = $this->get('website.detail_get')->fetch($params);
        //todo: better update the emar_webiste for caching...
        //  if the current_time - row.updated_at  < 1 hour, fetch from the database /
        return array('website'=> $website );
    }

    /**
     * @Route("/shopsearch")
     * @Method("GET");
     * @Template();
     */
    public function shopSearchAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');


            $keyword = $request->query->get('q');
            $search_web  =  array('rt'=>1,'q'=> $keyword);
        // todo: foward to shoplistpage if $keyword is empty.
        if( !isset($keyword ) || 0 >= strlen(trim($keyword))) {
            $url = $this->generateUrl('jili_emar_websites_shoplist') ;
            if(  $request->query->count() > 0 ) {
                $url .= '?'.http_build_query($request->query->all() );
            }
            return $this->redirect( $url );
        } 
        $wcat_id = $request->query->get('wcat' );
        $page_no = $request->query->getInt('p',1);
        // wcats with local file cache
        $wcats = $this->get('website.categories')->fetch() ;
        // webs 
        $websites = array();
        $params =array();

        if( isset($wcat_id ) && is_numeric($wcat_id) && $wcat_id > 0 ) {
            $params = array('catid'=> $wcat_id );
        }
        $web_raw  = $this->get('website.list_get')->fetch( $params );
        if( strlen(trim($keyword)) > 0) {
            $websites = $this->get('website.search')->find( $web_raw, $keyword );
        } else {
            $websites = $web_raw;
        }
        // pagination
        $total =  count($websites);
        $page_size = $this->container->getParameter('emar_com.page_size');
        $websites_paged = array_slice( $websites, ( $page_no -1 ) * $page_size  , $page_size );
        $webids = array_filter(array_unique( array_map( function($v) { if ( isset($v['web_id'])) { return  $v['web_id']; } ; } , $websites_paged)));
        // fetch the website information from the table emar_webistes_croned .
        if( count($webids ) > 0 ) {
            $webinfos_croned = $this->getDoctrine()->getRepository('JiliEmarBundle:EmarWebsitesCroned')->fetchInfosByWebIds( $webids);
        } else {
            $webinfos_croned =  array();
        }
        // add the websites categories, wcats with local file cache
        $wcats = $this->get('website.categories')->fetch() ;
        // todo: resort the websites by our configurations.
        return array('search_keyword'=> $keyword,
            'websites'=> $websites_paged,
            'websites_infos'=>$webinfos_croned,
            'categories'=> $wcats,
            'total'=>$total );
    }

     /**
      * @Route("/demo")
      * @Method("GET");
      */
     public function demoAction()
     {
         $em = $this->getDoctrine()->getManager();
         $this->get('cron.website_and_category')->truncate();
         for($i = 1 ; $i < 10 ; $i++ ){
             $wid = $i ; 
             for($j = 11;  $j< 21; $j++ ) {
                 $catid = $j;
                 $this->get('cron.website_and_category')->add($wid, $catid);
             }
         }

         for($i = 1 ; $i < 5 ; $i++ ){
             $wid = $i ; 
             for($j = 15;  $j< 21; $j++ ) {
                 $catid = $j;
                 $this->get('cron.website_and_category')->add($wid, $catid);
             }
         }

         for($i = 2 ; $i < 8 ; $i++ ){
             $wid = $i ; 
             for($j = 13;  $j< 18; $j++ ) {
                 $catid = $j;
                 $this->get('cron.website_and_category')->add($wid, $catid);
             }
         }
         $this->get('cron.website_and_category')->duplicateForQuery();
         // code...
         return new Response('ok');
     }
}

