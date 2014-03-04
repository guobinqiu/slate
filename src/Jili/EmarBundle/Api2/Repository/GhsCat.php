<?php
namespace Jili\EmarBundle\Api2\Repository;

class GhsCat {
  /**
   * @return: 返回数组，array( id=> name) 
   */
  public static function parse( array $rows ) {
    $mapings = array();
    foreach($rows as $row) {
      if(isset($row['ghs_catid']) && isset($row['ghs_cname'])  ) {
        $mapings[ (string) $row['ghs_catid'] ] = $row['ghs_cname'];
      }
    }
    return $mapings;
  }
}
