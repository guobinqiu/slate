<?php
namespace Jili\EmarBundle\Api2\Repository;

class HotWeb
{
  /**
   * @return: 取web_id
   */
  public static function getIds(array $rows)
  {
    $ids = array();
    foreach($rows as $row) {
      if( isset( $row['web_id'] )) {
        $ids[]  = $row['web_id'];
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
      if(isset($row['web_id']) && isset($row['web_name'])  ) {
        $mapings[(string)  $row['web_id'] ] = $row['web_name'];
      }
    }
    return $mapings;
  }
}
