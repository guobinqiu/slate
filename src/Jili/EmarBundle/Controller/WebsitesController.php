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

                $url = $this->generateUrl('jili_emar_websites_result') .'?'. http_build_query( array('q'=> $keyword ) ) ;
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
        $page_no = $request->query->get('p',1);

        $websites = array();
        $web_raw  = $this->get('website.list_get')->fetch( );
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $web_raw, true) );

        if( strlen(trim($keyword)) > 0) {
            $websites = $this->get('website.search')->find( $web_raw, $keyword );
        } else {
            $websites = $web_raw;
        }
        $total =  count($websites);

        $form = $this->createForm(new SearchType(), array('keyword'=>$keyword)  );
        $page_size = $this->container->getParameter('emar_com.page_size');
        $websites_paged = array_slice($websites, ( $page_no -1 ) * $page_size + 1 , $page_size      );
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

        $wcat_id = $request->request->get('wcat' );

        $keyword = $request->query->get('q', '');

        $page_no = $request->query->getInt('p',1);
        $page_no = ($page_no == 0 )?  $page_no : 1;


        // wcats
        $wcats = $this->get('website.categories')->fetch() ;
        $logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $wcats, true));

        // webs 
        $websites = array();
        $params =array();
        if( $wcat_id ) {
            $params = array('wcat_id'=> $wcat_id );
        }
        $web_raw  = $this->get('website.list_get')->fetch( $params );
        $websites = WebListRepository::parse( $web_raw);


        #$logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $web_raw, true) );

        $total =  count($websites);

        $page_size = $this->container->getParameter('emar_com.page_size_of_shoplist');
        $websites_paged = array_slice($websites, ( $page_no -1 ) * $page_size + 1 , $page_size );

        foreach($websites_paged as $w ) {

        }

        
        $logger->debug('{jarod}'. implode(':', array(__LINE__, __CLASS__,'')).var_export( $websites_paged , true) );

        return  array('categories'=> $wcats, 'websites'=> $websites_paged, 'total'=> $total);
        //return array();
    }
}
