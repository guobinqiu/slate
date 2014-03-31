<?php
namespace Jili\EmarBundle\EventListener;

use Jili\EmarBundle\Api2\Request;

class HotactivityCategoryGetRequest  extends GeneralRequest /* implements GeneralRequestInterface */  {

  public function get() {

    //todo: cached 
    $req = new  HotactivityCategoryGetRequest;
#                Jili\EmarBundle\Api2\Request\HotactivityCategoryGetRequest
    $req->setFields('hot_catid,hot_cname,modified_time');
    return  $this->c->exe($req);
  }
}


