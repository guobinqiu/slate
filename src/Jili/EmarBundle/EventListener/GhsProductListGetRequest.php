<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class GhsProductListGetRequest  extends BaseRequest{

  private $result;

  private $fields;
  private $page_size;
  private $total;

  public function fetch( $param = array()  ) {
    //todo: cached 
    $req = new  \Jili\EmarBundle\Api2\Request\GhsProductListGetRequest;
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

 //   $this->c->setApp($this->app_name);

    $resp =  $this->c->setApp($this->app_name)->exe($req);
    $this->logger->debug (implode(':', array( '{jarod}',__CLASS__, __LINE__,'')). var_export($this->c->getApp(), true)  );

    $result = array();
    if( isset( $resp[ 'ghs_list']) && isset($resp['ghs_list'] ['ghs'] ) ) {
      $result = $resp['ghs_list']['ghs'];
    }
    $this->result = $result;
    return $result;
  }

  public function setFields( $fields  = '' ) {
      $this->fields = (string )  $fields;
      return $this;
  }
  public function getTotal() {
      return $this->total;
  }
  public function setPageSize( $count ) {
    $this->page_size = (int)  $count;
    return $this;
  }

}


