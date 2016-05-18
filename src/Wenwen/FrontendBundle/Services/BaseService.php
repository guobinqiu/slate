<?php

namespace Wenwen\FrontendBundle\Services;

class BaseService
{
    protected $em;

    protected $logger;

    public function setEntityManager($em) {
        $this->em = $em;
    }

    public function setLogger($logger) {
        $this->logger = $logger;
    }
}