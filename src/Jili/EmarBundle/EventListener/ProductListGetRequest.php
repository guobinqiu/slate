<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;


class ProductListGetRequest  {

  private $logger;
  private $page_size;
  private $result;
  private $total;

  private $fields;


  function __construct() {
    $this->page_size = 0;
  }

  public function setFields( $fields  = '' ) {
      $this->fields = (string )  $fields;
      return $this;
  }
  /**
   *
   */
  public function fetch( $params = array() ) {

    $req=new \Jili\EmarBundle\Api2\Request\ProductListGetRequest;


    extract($params);

    if( strlen(trim($this->fields)) > 0) {
        $req->setFields($this->fields );
    }  else {
        //$req->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');
        $req->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total');
    }

    if( isset($catid) && ! empty($catid)) {
      $req->setCatid($catid);
    }
    if( isset($webid)  && ! empty($webid) ) {
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
    if( isset( $resp[ 'pdt_list']) && isset($resp['pdt_list'] ['pdt'] ) ) {
        $result = $resp['pdt_list']['pdt'];
        $this->result = $result;
    } else {
        $this->result = array(); // 
        #$this->logger->debug('{jarod}' . implode(':', array( __CLASS__,__LINE__,'')). var_export( $resp , true) );
    }

    $this->total = isset($resp['total'] ) ? $resp['total']: 0 ;
    return $this->result;
  }

  public function getTotal() {
      return $this->total;
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
