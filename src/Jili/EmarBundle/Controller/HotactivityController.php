<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Api2\Repository\HotCat as HotCatRepository;
use Jili\EmarBundle\Api2\Repository\HotWeb as HotWebRepository;
/**
 * @Route("/hotactivity", requirements={"_scheme" = "http"})
 */
class HotactivityController extends Controller
{
    #    /**
    #     * @Route("/index")
    #     * @Template();
    #     */
    #    public function indexAction() {
    #        $request = $this->get('request');
    #        $logger= $this->get('logger');
    #
    #
    #
    #        $websiteRequest =  $this->get('hotactivity.website_get');
    #        $websites_raw = $websiteRequest->fetch();
    #        $webids = HotWebRepository::getIds( $websites_raw);
    #        $webs = HotWebRepository::parse( $websites_raw);
    #
    #        $categoryRequest =  $this->get('hotactivity.category_get');
    #        $categories_raw  = $categoryRequest->fetch();
    #        $catids = HotCatRepository::getIds( $categories_raw);
    #        $cats = HotCatRepository::parse( $categories_raw);
    #
    #        $form = $this->createFormBuilder()
    #            ->add('category', 'choice', array( 'multiple'=> true,'expanded'=> true, 'choices' => $cats ))
    #            ->add('website', 'choice', array( 'multiple'=> true, 'expanded'=> true,'choices' => $webs ))
    #            ->getForm();
    #
    #
    #        $listRequest =  $this->get('hotactivity.list_get');
    #        $params =array('webid'=> implode(',',$webids), 'catid'=> implode(',', $catids));
    #        $list = $listRequest->fetch( $params );
    #
    #
    #        return array( 'webids'=> $webids, 'cats'=> $cats , 'form'=>$form->createView(), 'hotactivity'=> $list );
    #    }
    #

    #    /**
    #     * @Route("/filters")
    #     * @Template();
    #     */
    #    public function filtersAction(){
    #        $req = new  \Jili\EmarBundle\Api2\Request\HotactivityCategoryGetRequest;
    #        $websiteRequest =  $this->get('hotactivity.website_get');
    #        $websites_raw = $websiteRequest->fetch();
    #        $webs = HotWebRepository::parse( $websites_raw);
    #
    #        $categoryRequest =  $this->get('hotactivity.category_get');
    #        $categories_raw  = $categoryRequest->fetch();
    #        $cats = HotCatRepository::parse( $categories_raw);
    #        return array( 'webs'=> $webs, 'cats'=> $cats  );
    #    }

    /**
     * @Route("/partial-on-cps/{catids}", defaults={"catids"= "01"} )
     * @Template();
     */
    public function partialOnCpsAction($catids) {
        $request = $this->get('request');
        $logger= $this->get('logger');
        if( empty($catids)) {
            $catids = '01';
        }
        $cats = array();
        $list = array();
        return array('router_'=> 'jili_emar_top_cps', 'catids_request'=>explode(',',$catids), 'cats'=> $cats , 'hotactivity'=> $list );
        #    //todo: added to cache file.
        #    $categoryRequest =  $this->get('hotactivity.category_get');
        #    $categories_raw  = $categoryRequest->fetch();
        #    $cats = HotCatRepository::parse( $categories_raw);
        #    $listRequest =  $this->get('hotactivity.list_get');

        #    $params =array( 'catid'=> $catids);
        #    $list = $listRequest->fetch( $params );
        #    return array('router_'=> 'jili_emar_top_cps', 'catids_request'=>explode(',',$catids), 'cats'=> $cats , 'hotactivity'=> $list );
    }

    /**
     * @Route("/hot/{tmpl}/{max}", defaults={"tmpl"="top", "max"=3 }, requirements={"tmpl"="\w+", "max"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function hotAction($tmpl, $max ){
        $request = $this->get('request');
        $logger= $this->get('logger');


        #        $categoryRequest =  $this->get('hotactivity.category_get');
        #        $categories_raw  = $categoryRequest->fetch();
        #        $cats = HotCatRepository::parse( $categories_raw);
        #        $listRequest =  $this->get('hotactivity.list_get');
        #        $list = $listRequest->fetch( );

        $em = $this->getDoctrine()->getManager();
        $hot_webs = $em->getRepository('JiliEmarBundle:EmarWebsites')->getHot( array('limit'=>$max ));
        $webids =  array_map( function($v) { if ( isset($v['webId'])) { return  $v['webId']; } ; } , $hot_webs );
        $template ='JiliEmarBundle:Hotactivity:'. 'hot_on_'. $tmpl. '.html.twig';
        return $this->render($template, array('hotactivity'=> $list ));
    }

}
