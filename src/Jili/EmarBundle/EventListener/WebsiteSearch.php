<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;


class WebsiteSearch  {

  private $logger;
  private $listGet;
  private $detailGet;

  /**
   * @param $web_raw  the response from the emar open api.
   * @param $keyword the string to match the  web.web_name. 
   * TODO: match web.information
   * @return matched website array.
   */
  public function find( $web_raw , $keyword) {
      $keywords = preg_split("/[\s,]+/", $keyword );
      $matched = array();
      mb_regex_encoding('UTF-8');
      foreach( $web_raw as $web) {
          foreach( $keywords  as $k) {
              #$this->logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export(  '^.*?'.trim($k).'.*?$', true)  );
              $r = '^.*?'.trim($k).'.*?$';
              if(isset($web['web_name'] ) &&  true === mb_ereg_match( $r , $web['web_name'] ) ) {
                  $matched[] = $web;
                  continue;
              } else {
                  #$this->logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $r, true)  );
                  #$this->logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export(  $web['web_id'].' '.$web['web_name'], true)  );
              }
          }
      }
     $this->logger->debug('{jarod}'. implode(',', array(__CLASS__, __LINE__, '') ).var_export( $matched, true)  );
      return $matched;
  }

  public function setLogger(  LoggerInterface $logger) {
    $this->logger = $logger;
  }

  public function setDetailGet(  $detailGet) {
    $this->detailGet = $detailGet;
  }

  public function setListGet(  $listGet) {
    $this->listGet = $listGet;
  }
}
/*
        "web_list": {
            "web": [{
                "web_id": "3659",
                "web_name": "雅昌影像",
                "web_catid": "24",
                "logo_url": "http://image.yiqifa.com/ad_images/reguser/24/4/60/1376643810386.jpg",
                "web_o_url": "http://p.yiqifa.com/n?k=2mLErnWe6nDOrI6HCZg7Rnu_fmUmUSebRcgsRIeEYOsH2mLErntmWl2mrnzSWn2ernXH2mq_rI6H6E4b3NRFMEPH5toARcMJrj--&e=APImemberId&spm=139216929186018017.1.1.1",
                "commission": "10.5%"
            },

*/
