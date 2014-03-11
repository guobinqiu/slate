<?php
namespace Jili\EmarBundle\Api2\Repository;

class WebList{
  /**
   * 取web_id
   */
  public static function getIds( array $rows ) {
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
  public static function parse( array $rows ) {
    $mapings = array();
    foreach($rows as $row) {
        if( isset($row['web_id'])  && isset($row['web_name']) )   {

            $tmp  = array( 'name'=>$row['web_name']);

            if(isset($row['web_catid'] )) {
                $tmp[ 'catid']= $row['web_catid'] ;
            }

            if(isset($row['logo_url'] )) {
                $tmp[ 'logo']= $row['logo_url'] ;
            }

            if( isset($row['web_o_url'])) {
                $tmp[ 'url']= $row['web_o_url'];
            }

            $mapings[ (string) $row['web_id'] ] = $tmp;
        }
    }
    return $mapings;
  }

  /**
   * @return: 返回数组，array( id=> name) 
   */
  public static function parseByCat( array $rows ) {
      $mapings = array();

      foreach($rows as $ind => $row) {

          if( isset($row['web_id'])  && isset($row['web_name']) && isset($row['web_catid'] )  )   {

              if( ! isset($mapings[ (string) $row['web_catid'] ]) ) {
                  $mapings[ (string) $row['web_catid'] ] = array();
              }
              $mapings[ (string) $row['web_catid'] ] [ $row['web_id'] ] = array( 'name'=>$row['web_name'], 'logo'=>$row['logo_url'] , 'url'=> $row['web_o_url']);
          }
      }
      ksort($mapings);
      return $mapings;
  }



#  /**
#   * @param $cat_ind [1,...] , $categories_raw 数组。
#   */
#  public static function fetchIdByIndex( $web_ind, $web_raw) {
#        $web = (isset( $web_raw[ $web_ind - 1 ] )) ? $web_raw[ $web_ind - 1] : null;
#        $web_id = $web['web_id'];
#        return $web_id;
#  }

}

/*
 *cat

        "web_name": "雅昌影像",
        "web_catid": "24",
        "logo_url": "http://image.yiqifa.com/ad_images/reguser/24/4/60/1376643810386.jpg",
        "web_o_url": "http://p.yiqifa.com/n?k=2mLErnWe6nDOrI6HCZg7Rnu_fmUmUSebRcgsRIeEYOsH2mLErntmWl2mrnzSWn2ernXH3PP35wMwrJoH2mLOW9Bb6lb96QLE&spm=139216929186018017.1.1.1",
        "commission": "10.5%"
*/
