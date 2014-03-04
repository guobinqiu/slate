<?php

namespace Jili\EmarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Jili\EmarBundle\Api2\Repository\GhsCat as GhsCatRepository;

/**
 * @Route("/ghs")
 */
class GhsController extends Controller
{

  /**
   * @Route("/filters")
   * @Template();
   */
  public function filtersAction() {
    $request = $this->get('request');
    $logger= $this->get('logger');

    $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ) );


    #$req = new  \Jili\EmarBundle\Api2\Request\GhsCategoryGetRequest;

    $categoryRequest =  $this->get('ghs.category_get');
    $categories_raw  = $categoryRequest->fetch();

    $cats = GhsCatRepository::parse( $categories_raw);

    $logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $cats ,true)  );
    return array('cats'=> $cats);

  }

}
