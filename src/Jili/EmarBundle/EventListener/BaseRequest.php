<?php
namespace Jili\EmarBundle\EventListener;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class BaseRequest {

  protected $logger;

  protected $c;
  protected $app_name;

  public function setLogger(  LoggerInterface $logger) {
    $this->logger = $logger;
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
}

