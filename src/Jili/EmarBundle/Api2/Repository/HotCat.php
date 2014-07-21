<?php
namespace Jili\EmarBundle\Api2\Repository;

class HotCat
{
  /**
   * 取web_id
   */
  public static function getIds(array $rows)
  {
    $ids = array();
    foreach($rows as $row) {
      if( isset( $row['hot_catid'] )) {
        $ids[]  = $row['hot_catid'];
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
      if(isset($row['hot_catid']) && isset($row['hot_cname'])  ) {
        $mapings[ (string) $row['hot_catid'] ] = $row['hot_cname'];
      }
    }
    return $mapings;
  }
}
