<?php

namespace Wenwen\AppBundle\Services\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class EmailDeliveryLogger
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

}
