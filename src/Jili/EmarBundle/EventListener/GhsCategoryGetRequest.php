<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class GhsCategoryGetRequest  {

  private $logger;
  private $result;

  public function fetch() {
    //todo: cached 
    $req = new  \Jili\EmarBundle\Api2\Request\GhsCatGetRequest;
    $req->setFields('ghs_catid,ghs_cname,sort_order');
    $resp =  $this->c->exe($req);
    $result = array();

    if( isset( $resp[ 'ghs_cats']) && isset($resp['ghs_cats'] ['ghs_cat'] ) ) {
      $result = $resp['ghs_cats']['ghs_cat'];
    }

    $this->result = $result;
    return $result;
  }

  public function setLogger(  LoggerInterface $logger) {
    $this->logger = $logger;
  }

  public function setConnection( EmarRequestConnection  $c ) {
    $this->c = $c;
  }
}


