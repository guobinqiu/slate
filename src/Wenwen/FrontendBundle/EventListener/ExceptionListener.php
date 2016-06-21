<?php

namespace Wenwen\FrontendBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * 捕获Controller异常
 */
class ExceptionListener
{
    private $logger;

    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!$exception instanceof ResourceNotFoundException) {
            $this->logger->error($exception);
        }
    }
}