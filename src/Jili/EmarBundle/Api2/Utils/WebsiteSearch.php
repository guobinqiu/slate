<?php
namespace Jili\EmarBundle\Api2\Utils;

use Symfony\Component\HttpKernel\Log\LoggerInterface;


class WebsiteSearch
{
  private $logger;
  private $listGet;
  private $detailGet;
  const SAME_CATEGORY_LIMIT = 3;

  /**
   * @param $web_raw  the response from the emar open api.
   * @param $keyword the string to match the  web.web_name.
   * TODO: match web.information
   * @return matched website array.
   */
  public function find($web_raw , $keyword)
  {
      $keywords = preg_split("/[\s,]+/", $keyword );
      $matched = array();
      mb_regex_encoding('UTF-8');
      foreach( $web_raw as $web) {
          foreach( $keywords  as $k) {
              $r = '^.*?'.trim($k).'.*?$';
              if(isset($web['web_name'] ) &&  true === mb_ereg_match( $r , $web['web_name'] ) ) {
                  $matched[] = $web;
                  continue;
              } else {
              }
          }
      }
      return $matched;
  }

  public function findSameCatWebsites( $web_raw , $catid, $web_id)
  {
      $matched = array();
      foreach( $web_raw as $web) {
          if ($web['web_catid']==$catid && $web['web_id']!=$web_id){
              $matched[] = $web;
          }
          if(count($matched)>= self::SAME_CATEGORY_LIMIT) {
              break;
          }
      }
      return $matched;
  }
  public function setLogger(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  public function setDetailGet($detailGet)
  {
    $this->detailGet = $detailGet;
  }

  public function setListGet($listGet)
  {
    $this->listGet = $listGet;
  }
}
