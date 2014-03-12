<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;



class WebsiteListGetRequest  {

  private $logger;
  private $result;


  private $fields; 

  public function __construct()
  {
      $this->fields = '';
      return $this;
  }

  public function setFields( $fields  = '' ) {
      $this->fields = (string )  $fields;
      return $this;
  }

  public function fetch( array $params = array('wtype' => 1, 'catid' => '')) {

    extract($params);
    //todo: cached 
    $req = new  \Jili\EmarBundle\Api2\Request\WebsiteListGetRequest;

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

    $resp =  $this->c->exe($req);

    #$this->logger->debug('{jarod}'.implode( ':', array(__CLASS__ , __LINE__,'')) . var_export( $resp, true));

    $result = array();

    if( isset( $resp[ 'web_list']) && isset($resp['web_list'] ['web'] ) ) {
      $result = $resp['web_list']['web'];
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


