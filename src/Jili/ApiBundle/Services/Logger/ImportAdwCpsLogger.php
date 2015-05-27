<?php

namespace Jili\ApiBundle\Services\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class ImportAdwCpsLogger
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

}
