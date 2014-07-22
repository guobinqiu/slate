<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\EmarBundle\Api2\Request\GhsCatGetRequest as OpenApiGhsCatGetRequest;
class GhsCategoryGetRequest  extends BaseRequest
{
  public function fetch()
  {
    //todo: cached
    $req = new  OpenApiGhsCatGetRequest;

    $req->setFields('ghs_catid,ghs_cname,sort_order');
   // $resp =  $this->c->setApp($this->app_name)->exe($req);

    if( ! is_null($this->app_name)) {
        $this->c->setApp($this->app_name);
    }

    $resp = $this->getCached($req);

    if( empty($resp)) {
        $resp =  $this->c->exe($req);
        $this->updateCached($req, $resp);
    }

    $result = array();

    if( isset( $resp[ 'ghs_cats']) && isset($resp['ghs_cats'] ['ghs_cat'] ) ) {
      $result = $resp['ghs_cats']['ghs_cat'];
    }

    $this->result = $result;
    return $result;
  }
}
