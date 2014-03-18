<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

use Jili\EmarBundle\Form\Type\SearchType;

use Jili\EmarBundle\Api2\Repository\WebList as WebListRepository;

/**
 * @Route("/websites")
 */
class WebsitesController extends Controller
{
    /**
     * @Route("/search")
     * @Template();
     */
    public function searchAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');


        $form = $this->createForm(new SearchType() );
        if( $request->isMethod('post')) {

            $form->bind($request);
            if  ( $form->isValid()) {
                $query_params = $form->getData();
                $keyword = $query_params['keyword'];
                $url = $this->generateUrl('jili_emar_websites_result') .'?'. http_build_query( array('q'=> $keyword )) ;
                return $this->redirect( $url );
            }
        }
        return  array('form'=> $form->createView());
    }


    /**
     * @Route("/result")
     * @Template();
     */
    public function resultAction()
    {
        $request = $this->get('request');
        $logger= $this->get('logger');

        $keyword = $request->query->get('q', '');
        $page_no = $request->query->getInt('p',1);

        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $page_no, true) );

        $websites = array();
        $web_raw  = $this->get('website.list_get')->fetch( );

        if( strlen(trim($keyword)) > 0) {
            $websites = $this->get('website.search')->find( $web_raw, $keyword );
        } else {
            $websites = $web_raw;
        }

        $total =  count($websites);

        $form = $this->createForm(new SearchType(), array('keyword'=>$keyword)  );
        $page_size = $this->container->getParameter('emar_com.page_size');
        $websites_paged = array_slice($websites, ( $page_no -1 ) * $page_size  , $page_size      );

        return  array('form'=> $form->createView(), 'websites'=> $websites_paged, 'total'=> $total);
    }
    /**
     * @Route("/arrange")
     * @Template();
     */
    public function arrangeAction()
    {

        $logger = $this->get('logger');

        // find  out the 
       $category = $this->container->getParameter('emar_com.cps.category_type'); 

       $em = $this->getDoctrine()->getManager();

       $advertiserments = $this->getDoctrine()->getRepository('JiliApiBundle:Advertiserment')
           ->findBy(array( 'category'=> $category) );
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( count( $advertiserments ), true) );
       return new Response('ok');
       
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

        $form = $this->createForm(new SearchType(), array('keyword'=>$keyword)  );

        return  array('categories'=> $wcats, 'websites'=> $websites_paged, 'total'=> $total,'search_form'=>$form->createView() );
    }
}
