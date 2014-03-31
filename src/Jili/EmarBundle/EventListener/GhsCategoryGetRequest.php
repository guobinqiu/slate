<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class GhsCategoryGetRequest  extends BaseRequest {

#  private $logger;
  private $result;

  public function fetch() {
    //todo: cached 
    $req = new  \Jili\EmarBundle\Api2\Request\GhsCatGetRequest;
    $req->setFields('ghs_catid,ghs_cname,sort_order');
    $resp =  $this->c->setApp($this->app_name)->exe($req);
    $this->logger->debug (implode(':', array( '{jarod}',__CLASS__, __LINE__,'')). var_export($this->c->getApp(), true)  );
    $result = array();

    if( isset( $resp[ 'ghs_cats']) && isset($resp['ghs_cats'] ['ghs_cat'] ) ) {
      $result = $resp['ghs_cats']['ghs_cat'];
    }

    $this->result = $result;
    return $result;
  }

  #
  #public function setLogger(  LoggerInterface $logger) {
#    $this->logger = $logger;
#    return $this;
#  }

}


