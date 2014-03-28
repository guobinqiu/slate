<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

use Jili\EmarBundle\Form\Type\SearchType;
use Jili\EmarBundle\Form\Type\SearchWebsiteType;

use Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

/**
 * @Route("/websites")
 */
class WebsitesController extends Controller
{
    /**
     * @Route("/hot")
     * @Template();
     */
    public function hotOnCpsAction()
    {

        $logger= $this->get('logger');

// wids.
// fetch the details ? 
        $em = $this->getDoctrine()->getManager();
        $hot_webs = $em->getRepository('JiliEmarBundle:EmarWebsites')->getHot();

        if(! empty($hot_webs)) {
            $webids = array_unique( array_map( function($v) { if ( isset($v['webId'])) { return  $v['webId']; } ; } , $hot_webs ));

            $websites = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->fetchByWebIds( $webids );

        } else {
            $websites = array();
        }
            $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $websites , true) );

        return compact('websites');
    }

    /**
     * @Route("/search")
     * @Template();
     */
    public function searchAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');


        if( $request->isMethod('post')) {
            $form = $this->createForm(new SearchType() );
            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = $query_params['keyword'];
                $url = $this->generateUrl('jili_emar_websites_result') .'?'. http_build_query( array('q'=> $keyword )) ;
                return $this->redirect( $url );
            }
        } else {
            $keyword = $request->query->get('q');
        }

        if( !isset($keyword ) || 0 >= strlen(trim($keyword))) {
            $url = $this->generateUrl('jili_emar_websites_shoplist') ;
            if(  $request->query->count() > 0 ) {
                $url .= '?'.http_build_query($request->query->all() );
            }
            return $this->redirect( $url );
        } 

        $form = $this->createForm(new SearchGeneralType() , array('q'=> $keyword) );

        return  array('form'=> $form->createView());
    }


    /**
     * @Route("/shoplist/search")
     * @Template();
     */
    public function shopListSearchAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');

        $wcat_id = $request->query->get('wcat' );

        $form = $this->createForm(new SearchType() );
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

                #$logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $url, true) );
                return $this->redirect( $url );
            }
        }
        return  array('form'=> $form->createView());
    }

    /**
     * @Route("/shoplist")
     * @Template();
     */
    public function shopListAction()
    {
        if(!  $this->get('request')->getSession()->get('uid') ) {
            return  $this->redirect($this->generateUrl('_user_login'));
        }


        $request = $this->get('request');
        $logger= $this->get('logger');
        $em = $this->getDoctrine()->getManager();

        $wcat_id = $request->query->get('wcat' );
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

        // searching 
        if( strlen(trim($keyword)) > 0) {
            $web_searched = $this->get('website.search')->find( $web_raw, $keyword );
            $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $web_searched, true) );
            $websites = WebListRepository::parse( $web_searched);
        } else {
            $websites = WebListRepository::parse( $web_raw);

        }



        $webids =  array_keys($websites);
        $params ['wids'] = $webids;

        //todo: use a index of  type  array (  wid=>index  );
        //todo: add catid into where
        $websites_configed = $em->getRepository('JiliEmarBundle:EmarWebsites')->getSortedByParams( $params );

        #$logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $websites, true) );

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


        // update the commission
        $websites_configed_wid = array();

        foreach($websites_configed as $row ) {
            $websites_configed_wid [$row->getWebId() ] = $row; 
        }

        foreach ($websites_paged as $k => $v) {
            if( isset( $websites_configed_wid[$k] )) {
                $row = $websites_configed_wid[$k];
                if( 0 <  strlen(trim($row->getCommission()))) {
                    $websites_paged[$k] ['commission'] =  $row -> getCommission() ;
                }
            }
        }

        $filter_form = $this->createForm(new SearchType(), array('keyword'=>$keyword)  );

        $search_form = $this->createForm(new SearchWebsiteType() , array('q'=> $request->query->get('q')) );

        return  array('categories'=> $wcats, 'websites'=> $websites_paged, 'total'=> $total,'filter_form'=>$filter_form->createView(),
            'search_form'  => $search_form ->createView() );
    }

    /**
     * @Route("/detail/{wid}", requirements={"wid" = "\d+"}, defaults={"wid" = 0})
     * @Template();
     * todo: added pageno for recommend
     */
    public function detailAction($wid )
    {

        if(!  $this->get('request')->getSession()->get('uid') ) {
            return  $this->redirect($this->generateUrl('_user_login'));
        }

        $request = $this->get('request');
        $logger= $this->get('logger');

        $params = array('webid'=>$wid );
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $params, true) );

        $website = $this->get('website.detail_get')->fetch($params);
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $website, true) );

        //todo: better update the emar_webiste for caching...
        //  if the current_time - row.updated_at  < 1 hour, fetch from the database /


        return array('website'=> $website );
    }

    /**
     * @Route("/search/form")
     * @Template();
     */
    public function searchFormAction()
    {
        $form = $this->createForm(new SearchWebsiteType() );
        return array( 'form' => $form->createView() );
    }

    /**
     * @Route("/shopsearch")
     * @Template();
     */
    public function shopSearchAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');

        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')) );

        if( $request->isMethod('post')) {
        $form = $this->createForm(new SearchWebsiteType() );
            $form->bind($request);
            if ( $form->isValid()) {
                $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')) );
                $query_params = $form->getData();

                $keyword = $query_params['q'];

                $router = $query_params['rt']; // must eq 1 
                unset($query_params['rt']);

                //todo: search the result 
                if( $router == 0 ) {
                    $url = $this->generateUrl('jili_emar_product_search');
                } else if( $router == 1 ) {
                    $url = $this->generateUrl('jili_emar_websites_shopsearch');
                }

                $query = array_merge( $request->query->all(), $query_params );
                return $this->redirect( $url .'?'.http_build_query( $query));
                
            }
        }  else {
            $keyword = $request->query->get('q');
            $search_web  =  array('rt'=>1,'q'=> $keyword);
            $form = $this->createForm(new SearchWebsiteType(), $search_web);
        } 
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
        //$logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $wcats, true) );
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
        #$logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'total','')).var_export( $webids, true) );
        #$logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'total','')).var_export( count($websites), true) );
        // fetch the website information from the table emar_webistes_croned .
        if( count($webids ) > 0 ) {
            $webinfos_croned = $this->getDoctrine()->getRepository('JiliEmarBundle:EmarWebsitesCroned')->fetchInfosByWebIds( $webids);
        } else {
            $webinfos_croned =  array();
        }
        #$logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'webinfo','')).var_export( $webinfos_croned, true) );
        // add the websites categories, wcats with local file cache
        $wcats = $this->get('website.categories')->fetch() ;
        // todo: resort the websites by our configurations.
        return array( 'search_form' => $form->createView() , 
            'search_keyword'=> $keyword,
            'websites'=> $websites_paged,
            'websites_infos'=>$webinfos_croned,
            'categories'=> $wcats,
            'total'=>$total );
    }
}

#    /**
#     * @Route("/result")
#     * @Template();
#     */
#    public function resultAction()
#    {
#        $request = $this->get('request');
#        $logger= $this->get('logger');
#
#        $keyword = $request->query->get('q', '');
#        $page_no = $request->query->getInt('p',1);
#
#
#        $websites = array();
#        $web_raw  = $this->get('website.list_get')->fetch( );
#
#        if( strlen(trim($keyword)) > 0) {
#            $websites = $this->get('website.search')->find( $web_raw, $keyword );
#        } else {
#            $websites = $web_raw;
#        }
#
#        $total =  count($websites);
#
#        $form = $this->createForm(new SearchType(), array('keyword'=>$keyword)  );
#        $page_size = $this->container->getParameter('emar_com.page_size');
#        $websites_paged = array_slice($websites, ( $page_no -1 ) * $page_size  , $page_size      );
#
#        return  array('form'=> $form->createView(), 'websites'=> $websites_paged, 'total'=> $total);
#    }
#
#    /**
#     * @Route("/arrange")
#     * @Template();
#     */
#    public function arrangeAction()
#    {
#
#        $logger = $this->get('logger');
#
#        // find  out the 
#        $category = $this->container->getParameter('emar_com.cps.category_type'); 
#
#        $em = $this->getDoctrine()->getManager();
#
#        $advertiserments = $this->getDoctrine()->getRepository('JiliApiBundle:Advertiserment')
#            ->findBy(array( 'category'=> $category) );
#        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( count( $advertiserments ), true) );
#        return new Response('ok');
#
#    }
