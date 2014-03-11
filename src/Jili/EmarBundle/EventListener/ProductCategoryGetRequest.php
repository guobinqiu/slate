<?php

namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;



class ProductCategoryGetRequest  {

  private $logger;
  private $result;

  public function fetch(array $params = array()) {
      extract($params);
    //todo: cached 
    $req = new  \Jili\EmarBundle\Api2\Request\ProductCategoryGetRequest;

    $req->setFields('catid,cname,parent_id,alias,is_parent,modified_time');
    if( isset($parent_id) && ! empty($parent_id)) {
      $req->setParent_id($parent_id);
    }

    $resp =  $this->c->exe($req);
    $result = array();

    if( isset( $resp[ 'item_cats']) && isset($resp['item_cats'] ['item_cat'] ) ) {
      $result = $resp['item_cats']['item_cat'];
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


