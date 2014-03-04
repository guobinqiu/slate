<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Api2\Repository\HotCat as HotCatRepository;
use Jili\EmarBundle\Api2\Repository\HotWeb as HotWebRepository;
/**
 * @Route("/hotactivity")
 */
class HotactivityController extends Controller
{

  /**
   * @Route("/index")
   * @Template();
   */
  public function indexAction() {
    $request = $this->get('request');
    $logger= $this->get('logger');

    $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ) );
    #$logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export($request->request, true) ); //POST
    
    $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export($request->query, true) ); //GET

    $websiteRequest =  $this->get('hotactivity.website_get');
    $websites_raw = $websiteRequest->fetch();
    $webids = HotWebRepository::getIds( $websites_raw);
    $webs = HotWebRepository::parse( $websites_raw);

    $categoryRequest =  $this->get('hotactivity.category_get');
    $categories_raw  = $categoryRequest->fetch();
    $catids = HotCatRepository::getIds( $categories_raw);
    $cats = HotCatRepository::parse( $categories_raw);

    $form = $this->createFormBuilder()
      ->add('category', 'choice', array( 'multiple'=> true,'expanded'=> true, 'choices' => $cats ))
      ->add('website', 'choice', array( 'multiple'=> true, 'expanded'=> true,'choices' => $webs ))
      ->getForm();


    $listRequest =  $this->get('hotactivity.list_get');
    $params =array('webid'=> implode(',',$webids), 'catid'=> implode(',', $catids));
    $list = $listRequest->fetch( $params );

    $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($params, true)  );
    $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($list, true)  );

    return array( 'webids'=> $webids, 'cats'=> $cats , 'form'=>$form->createView(), 'hotactivity'=> $list );
  }


  /**
   * @Route("/filters")
   * @Template();
   */
  public function filtersAction(){

    $req = new  \Jili\EmarBundle\Api2\Request\HotactivityCategoryGetRequest;

    $websiteRequest =  $this->get('hotactivity.website_get');
    $websites_raw = $websiteRequest->fetch();
    $webs = HotWebRepository::parse( $websites_raw);

    $categoryRequest =  $this->get('hotactivity.category_get');
    $categories_raw  = $categoryRequest->fetch();
    $cats = HotCatRepository::parse( $categories_raw);


    return array( 'webs'=> $webs, 'cats'=> $cats  );

  }
  /**
   * @Route("/list")
   * @Template();
   */
  public function listAction(){

  }
}
