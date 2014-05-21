<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Jili\EmarBundle\Api2\Request\WebsiteCategoryGetRequest as OpenApiWebsiteCategoryGetRequest;

class WebsiteCategoryGetRequest extends BaseRequest {


  public function fetch( array $params  = array('wtype' => 1)) {
      extract($params);
    //todo: cached 
    $req = new  OpenApiWebsiteCategoryGetRequest;
    $req->setFields('web_catid,web_cname,amount,web_type,modified_time');
    $req->setWtype( $wtype );
    $resp = $this->getCached($req);
    if( empty($resp)) {
        $resp =  $this->c->exe($req);
        $this->updateCached($req, $resp);
    }

    $result = array();

    if( isset( $resp[ 'web_cats']) && isset($resp['web_cats'] ['web_cat'] ) ) {
      $result = $resp['web_cats']['web_cat'];
    }

    $this->result = $result;
    return $result;
  }

}


