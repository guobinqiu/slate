<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\EmarBundle\Api2\Request\GhsProductListGetRequest as OpenApiGhsProductListGetRequest;

class GhsProductListGetRequest  extends BaseListRequest {

    /**
     * @abstract: wrapper for the fetch() function, for duplicated pid in the fetch result.
     *  Because the  ghs_o_url prefixed with "m." in raw response will redirect to error page.
     */
    public function fetchDistinct( $param = array() ) {
        $page_size = $this->page_size;
        $this->setPageSize ( 2 * $page_size);

        $result = $this->fetch($param);

        $list  = array();
        foreach($result as $index => $row) {
            if( $index % 2 === 0 ) {
                $list [] = $row;
            }
            if( count($list) === $page_size) {
                break;
            }
        }
        return $list;
    }

    /**
     *
     */
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

    $resp = $this->getCached($req);

    if( empty($resp)) {
        $resp =  $this->c->setApp($this->app_name)->exe($req);
#        $this->logger->debug('{jarod}' . implode(':', array(__CLASS__, __LINE__, ' updated cache') ));
        $this->updateCached($req, $resp);
    }

    if( isset( $resp[ 'ghs_list']) && isset($resp['ghs_list'] ['ghs'] ) ) {
        $result = $resp['ghs_list']['ghs'];
    } else {
//        $cache->remove($cache_key); 
    }

    $this->result = $result;
    $this->total = isset($resp['total'] ) ? $resp['total']: 0 ;

    return $result;
  }

}
