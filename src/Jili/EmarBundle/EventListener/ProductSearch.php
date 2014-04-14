<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\EmarBundle\Api2\Request\ProductSearchGetRequest as  OpenApiProductSearch;

class ProductSearch {

  private $logger;
  private $page_size;
  private $result;
  private $total;

  function __construct() {
    $this->page_size = 0;
  }

  /**
   *
   */
  public function fetch( $params = array() ) {

    $req = new OpenApiProductSearch;

    $req->setFields('pid,p_name,web_id,web_name,ori_price,cur_price,pic_url,catid,cname,p_o_url,total,short_intro');

    extract($params);

    if( isset($keyword) && ! empty($keyword)) {

        $req->setKeyword(mb_convert_encoding($keyword,'gb2312', 'utf8'));

    } else {
        return array();
    }


    if( isset($catid) && ! empty($catid)) {
      $req->setCatid($catid);
    }

    if( isset($webid)  && ! empty($webid)  && $webid > 0  ) {
      $req->setWebid($webid);
    }

    if( isset($price_range) &&  0 < strlen(trim($price_range) ) ) {
      $req->setPrice_range($price_range);
    }

    if( isset($page_no) && $page_no > 0 ) {
      $req->setPage_no($page_no);
    }

    if( isset($orderby) && in_array($orderby , range(1,3))) {
        $req->setOrderBy($orderby);
    }

    if( 0 < $this->page_size ) {
      $req->setPage_size( $this->page_size );
    }

    $resp =  $this->c->exe( $req );

    #$this->logger->debug('{jarod}' . implode(':', array(__LINE__, __CLASS__,'')). var_export( $resp , true) );

    $result = array();
    if( isset( $resp[ 'pdt_list']) && isset($resp['pdt_list'] ['pdt'] ) ) {
      $result = $resp['pdt_list']['pdt'];
    }

    $this->result = $result;
    $this->total = isset($resp['total'] ) ? $resp['total']: null;

    return $result;
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
