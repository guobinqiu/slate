<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;



class HotactivityCategoryGetRequest  {

  private $logger;
  private $result;

  public function fetch() {
    //todo: cached 
    $req = new  \Jili\EmarBundle\Api2\Request\HotactivityCategoryGetRequest;
    $req->setFields('hot_catid,hot_cname,modified_time');
    $resp =  $this->c->exe($req);
    $result = array();

    if( isset( $resp[ 'hot_cats']) && isset($resp['hot_cats'] ['hot_cat'] ) ) {
      $result = $resp['hot_cats']['hot_cat'];
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


