<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;



class WebsiteDetailGetRequest  {

  private $logger;
  private $result;

  public function fetch( array $params = array('webid' => '')) {
    extract($params);
    //todo: cached 
    $req = new  \Jili\EmarBundle\Api2\Request\WebsiteGetRequest;
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

    $resp =  $this->c->exe($req);

    #$this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $resp, true));

    $result = array();

    if( isset( $resp[ 'website'])  ) {
      $result = $resp['website'];
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


