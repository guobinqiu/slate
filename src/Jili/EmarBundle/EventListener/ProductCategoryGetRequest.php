<?php

namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\EmarBundle\Api2\Request\ProductCategoryGetRequest as OpenApiProductCategoryGetRequest;


class ProductCategoryGetRequest  extends BaseRequest  {


  public function fetch(array $params = array()) {
      extract($params);
    //todo: cached 
    $req = new  OpenApiProductCategoryGetRequest;

    $req->setFields('catid,cname,parent_id,alias,is_parent,modified_time');
    if( isset($parent_id) && ! empty($parent_id)) {
      $req->setParent_id($parent_id);
    }

    $resp = $this->getCached($req);

    if( empty($resp)) {
        $resp =  $this->c->exe($req);
        $this->updateCached($req, $resp);
    }
    $result = array();

    if( isset( $resp[ 'item_cats']) && isset($resp['item_cats'] ['item_cat'] ) ) {
      $result = $resp['item_cats']['item_cat'];
    }

    $this->result = $result;
    return $result;
  }

}


