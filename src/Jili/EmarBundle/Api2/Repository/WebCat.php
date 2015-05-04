<?php
namespace Jili\EmarBundle\Api2\Repository;

class WebCat
{
  /**
   * 取web_id
   */
  public static function getIds(array $rows)
  {
    $ids = array();
    foreach($rows as $row) {
      if( isset( $row['web_catid'] )) {
        $ids[]  = $row['web_catid'];
      }
    }

    return $ids;
  }
  /**
   * @return: 返回数组，array( id=> name)
   */
  public static function parse(array $rows)
  {
    $mapings = array();
    foreach($rows as $row) {
      if(isset($row['web_catid']) && isset($row['web_cname'])  ) {
          $mapings[ (string) $row['web_catid'] ] = array('name'=>$row['web_cname'] /*,'amount'=> $row['amount']*/) ;
      }
    }
    return $mapings;
  }

}
