<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Jili\EmarBundle\EventListener\Cache\EmarResponse;

class BaseRequest {

  protected $result;
  protected $logger;

  protected $c;
  protected $app_name;
  protected $cache_proxy;

  protected $fields;

  public function setLogger(  LoggerInterface $logger) {
    $this->logger = $logger;
    $this->cache_proxy = null;
    return $this;
  }
  public function setConnection( EmarRequestConnection  $c ) {
    $this->c = $c;
    return $this;
  }
  public function setApp( $app_name = '' ) {
    $this->app_name = $app_name;
    return $this;
  }

  public function setFields( $fields  = '' ) {
      $this->fields = (string )  $fields;
      return $this;
  }

  /**
   * @param:$proxy could be a file
   * todo: use an interface should be more extendable.
   */
  public function setCacheProxy( EmarResponse $proxy )
  {
      $this->cache_proxy = $proxy; 
  }

  /**
   * @param: $req the EmarRequest instance 
   */
  protected function getCached( $req )
  {
      $result= array();
      if( isset($this->cache_proxy)) {
          $cache = $this->cache_proxy;
          $cache->setEmarRequest( $req );

          if( $cache->isValid()) {
              $resp = $cache->get(); 
          } 

      }
      return $result;
  }
  /**
   * @param: $req the EmarRequest instance 
   * @param: $resp is the emar response.
   */
  protected function updateCached($req, $resp) {
      $cache = $this->cache_proxy;
      $cache->setEmarRequest( $req );
      $cache->remove(); 
      return $cache->set( $resp); 
  }
}

