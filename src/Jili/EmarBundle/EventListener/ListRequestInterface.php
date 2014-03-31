<?php
namespace Jili\EmarBundle\EventListener;

interface ListRequestInterface {
    public function setPageSize( $count );
    public function getTotal() ;
}
