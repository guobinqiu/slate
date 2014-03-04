<?php
namespace Jili\EmarBundle\Api2\Repository;

class ItemCat {
  /**
   * 取web_id
   */
  public static function getIds( array $rows ) {
    $ids = array();
    foreach($rows as $row) {
      if( isset( $row['catid'] )) {
        $ids[]  = $row['catid'];
      }
    }

    return $ids;
  }
  /**
   * @return: 返回数组，array( id=> name) 
   */
  public static function parse( array $rows ) {
    $mapings = array();
    foreach($rows as $row) {
      if(isset($row['catid']) && isset($row['cname'])  ) {
        $mapings[ (string) $row['catid'] ] = $row['cname'];
      }
    }
    return $mapings;
  }
#  /**
#   * @param $cat_ind [1,...] , $categories_raw 数组。
#   */
#  public static function fetchIdByIndex( $cat_ind, $categories_raw) {
#
#      $cat= ( isset($categories_raw[ $cat_ind - 1 ]) ) ? $categories_raw[ $cat_ind - 1] : null;
#       # $this->logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($categories_raw, true) );
#
#        #$this->logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ). var_export($cat_id, true) );
#      $cat_id = $cat['catid'];
#
#      return $cat_id;
#  }
}
