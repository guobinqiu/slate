<?php
namespace Jili\EmarBundle\Api2\Repository;

class ItemCat {

  /**
   * 取web ids
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

  /**
   * 取cname 
   * 131010000
   * 131000000
   */
  public static function getCrumbsByScatid( $cats , $cid_2 ) {
      //$cid_2 = '131010000';
      $len =  strlen($cid_2);
      
      $len_parent = 4;

      $cname = '';

      if( $len > $len_parent) {

          $cid_1 = substr($cid_2, 0,$len_parent).  str_repeat('0', $len- $len_parent);;

          if( isset($cats[$cid_1]) && isset($cats[$cid_1][$cid_2] ) ) {

              $cname = $cats[$cid_1][$cid_2];
          }
      }

      return $cname;//array( $cid_1, $cid_2);
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
