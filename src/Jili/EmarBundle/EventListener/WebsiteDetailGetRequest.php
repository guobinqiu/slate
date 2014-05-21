<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\EmarBundle\Api2\Request\WebsiteGetRequest  as OpenApiWebsiteGetRequest;

class WebsiteDetailGetRequest  extends BaseRequest {
  public function fetch( array $params = array('webid' => '')) {
    extract($params);
    //todo: cached 
    $req = new  OpenApiWebsiteGetRequest;
    $req->setFields('web_id,web_name,web_catid,logo_url,web_url,information,begin_date,end_date,commission');

    if(! isset($wtype)) {
        $req->setWtype( 1);
    } else {
        $req->setWtype( $wtype );
    }

    if( strlen($webid) == 0) {
        return array();
    }

    $req->setWebid($webid);

    if( ! is_null($this->app_name ) ) {
        $this->c->setApp( $this->app_name );
    }

    $resp = $this->getCached($req);
    if( empty($resp)) {
        $resp =  $this->c->exe($req);
        $this->updateCached($req, $resp);
    }

    $result = array();

    if( isset( $resp[ 'website'])  ) {
      $result = $resp['website'];
    }

    $this->result = $result;
    return $result;
  }
}


