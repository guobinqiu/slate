<?php

namespace Jili\ApiBundle\Services\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class UserSourceLogger 
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger() {
        return $this->logger;
    }
}
