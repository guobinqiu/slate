<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;



class WebsiteCategoryGetRequest  {

  private $logger;
  private $result;

  public function fetch( $wtype = 1) {
    //todo: cached 
    $req = new  \Jili\EmarBundle\Api2\Request\WebsiteCategoryGetRequest;
    $req->setFields('web_catid,web_cname,amount,web_type,modified_time');
    $req->setWtype( $wtype );


    $resp =  $this->c->exe($req);
    $result = array();

    if( isset( $resp[ 'web_cats']) && isset($resp['web_cats'] ['web_cat'] ) ) {
      $result = $resp['web_cats']['web_cat'];
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


