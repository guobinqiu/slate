<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

use Jili\EmarBundle\Api2\Request\WebsiteListGetRequest as OpenApiWebsiteListGetRequest;


class WebsiteListGetRequest  extends BaseListRequest
{
  public function fetch( array $params = array('wtype' => 1, 'catid' => ''))
  {
    extract($params);
    //todo: cached
    $req = new  OpenApiWebsiteListGetRequest;

    if( strlen(trim($this->fields)) > 0) {
        $req->setFields($this->fields );
    }  else {
        $req->setFields('web_id,web_name,web_catid,logo_url,web_o_url,commission,total');
    }


    if(! isset($wtype)) {
        $req->setWtype( 1);
    } else {
        $req->setWtype( $wtype );
    }

    if(isset($catid) &&  strlen( $catid) > 0) {
      $req->setCatid($catid);
    } else {
      $req->setCatid('1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26');
    }

    $resp = $this->getCached($req);

    if( empty($resp)) {
        $resp =  $this->c->exe($req);
        $this->updateCached($req, $resp);
    }

    $result = array();

    if( isset( $resp[ 'web_list']) && isset($resp['web_list'] ['web'] ) ) {
      $result = $resp['web_list']['web'];
    }

    $this->result = $result;
    return $result;
  }

}
