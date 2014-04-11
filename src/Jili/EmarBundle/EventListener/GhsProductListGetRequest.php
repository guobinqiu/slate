<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\EmarBundle\Api2\Request\GhsProductListGetRequest as OpenApiGhsProductListGetRequest;

class GhsProductListGetRequest  extends BaseListRequest {
  public function fetch( $param = array()  ) {

    //todo: cached 
    $req = new  OpenApiGhsProductListGetRequest;
    extract($param);

// category     String[]    N   购划算商品分类，可选值，多个字段用","分隔；值可以通过open.ghs.cat.get获取，默认为空，表示获取全部类目的商品
    if( isset($category) && ! empty($category)) {
      $req->setCategory($category);
    }

    if( strlen(trim($this->fields)) > 0) {
        $req->setFields($this->fields );
    }  else {
        $req->setFields('pid,web_name,p_name,ghs_o_url,ghs_catid,ghs_cname,weight,ori_price,ghs_price,discount,bought,pic_url,post,begin_time,end_time,total');
    }

    if( isset($page_no) && $page_no > 0 ) {
      $req->setPage_no($page_no);
    }

    if( 0 < $this->page_size ) {
      $req->setPage_size($this->page_size );
    }

    $resp =  $this->c->setApp($this->app_name)->exe($req);
    #$this->logger->debug (implode(':', array( '{jarod}',__CLASS__, __LINE__,'')). var_export($this->c->getApp(), true)  );

    $result = array();
    if( isset( $resp[ 'ghs_list']) && isset($resp['ghs_list'] ['ghs'] ) ) {
        $result = $resp['ghs_list']['ghs'];
    }
    $this->result = $result;

    $this->total = isset($resp['total'] ) ? $resp['total']: 0 ;

    return $result;
  }

}
