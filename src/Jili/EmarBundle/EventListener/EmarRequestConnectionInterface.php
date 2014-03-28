<?php

namespace Jili\EmarBundle\EventListener;


interface EmarRequestConnectionInterface {

  public function setApp( $app_name  = '');
  public function exe( $req);

}
