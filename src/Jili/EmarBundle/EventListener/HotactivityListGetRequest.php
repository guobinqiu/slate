<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;


class HotactivityListGetRequest  {

  private $logger;
  private $page_size;


  function __construct() {
    $this->page_size = 0;
  }
  /**
   *
   */
  public function fetch( $params = array() ) {
    $req=new \Jili\EmarBundle\Api2\Request\HotactivityListGetRequest;
    $req->setFields('hot_id,web_id,web_name,hot_name,pic_url,hot_o_url,discount,brand_name,hot_catid,begin_date,end_date,modified_time,total');
    extract($params);

    if( isset($catid) ) {
      $req->setCatid($catid);
    }
    if( isset($webid) ) {
      $req->setWebid($webid);
    }

    if( isset($price_range) &&  0 < strlen(trim($price_range) ) ) {
      $req->setPrice_range($price_range);
    }

    if( isset($page_no) && $page_no > 0 ) {
      $req->setPage_no($page_no);
    }

    if( 0 < $this->page_size ) {
      $req->setPage_size($this->page_size );
    }

    $resp =  $this->c->exe( $req );

    #$this->logger->debug('{jarod}' . implode(':', array(__LINE__, __CLASS__,'')). var_export( $resp , true) );

    $result = array();
    if( isset( $resp[ 'hot_list']) && isset($resp['hot_list'] ['hot'] ) ) {
      $result = $resp['hot_list']['hot'];
    }
    return $result;
  }

  public function setLogger(  LoggerInterface $logger) {
    $this->logger = $logger;
  }

  public function setConnection( EmarRequestConnection  $c ) {
    $this->c = $c;
  }

  public function setPageSize( $count ) {
    $this->page_size = (int)  $count;
  }
}
