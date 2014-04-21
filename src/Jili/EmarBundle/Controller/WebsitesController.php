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
     * @Method({"GET","POST"})
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
        return $this->render('JiliEmarBundle:Websites:shoplist_search_form.html.twig', array( 'form'=> $form->createView( ) ) );
    }

    /**
     * @abstract: 会将本地配置的商家排列在前面。
     * @Route("/shoplist")
     * @Method( {"GET","POST"})
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
            $comm = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->parseMaxComission($websites_paged[$k] ['commission'] );

            if( isset( $websites_configed_wid[$k] )) {
                $row = $websites_configed_wid[$k];
                if( 0 <  strlen(trim($row->getCommission()))) {
                    $multiple = $row->getCommission();
                }
            } 

            if( ! isset($multiple) || $multiple === '' || $multiple === 0 || is_null($multiple)) {
                $multiple = $this->container->getParameter('emar_com.cps.action.default_rebate');
            }

            $websites_paged[$k] ['commission'] = round($comm * $multiple /100, 2);
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
                $comm = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->parseMaxComission($row->getCommission() );
                if( array_key_exists( $web_id,  $commissions) ){
                    $multiple = $commissions[$web_id];
                }
                if( ! isset($multiple) ||   is_null($multiple) || $multiple == 0   ){
                    $multiple = $this->container->getParameter('emar_com.cps.action.default_rebate');
                } 
                #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'comm','')) . var_export( $comm, true));
                #$logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'multiple','')) . var_export( $multiple, true));
                $row->setCommission( round($multiple * $comm /100, 2) );
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
     * @Method({"GET","POST"});
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

}

