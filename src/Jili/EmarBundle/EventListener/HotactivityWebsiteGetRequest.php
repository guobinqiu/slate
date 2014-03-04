<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;


class HotactivityWebsiteGetRequest  {

  private $logger;
  private $result ;

  public function fetch() {

    $req = new  \Jili\EmarBundle\Api2\Request\HotactivityWebsiteGetRequest;
    $req->setFields('web_id,web_name,web_o_url,modified_time');
    $resp =  $this->c->exe( $req );
    $result = array();

    if( isset( $resp[ 'hot_webs']) && isset($resp['hot_webs'] ['hot_web'] ) ) {
      $result = $resp['hot_webs']['hot_web'];
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


