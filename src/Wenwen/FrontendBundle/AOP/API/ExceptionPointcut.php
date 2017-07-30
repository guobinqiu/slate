<?php

namespace Wenwen\FrontendBundle\AOP\API;

use JMS\AopBundle\Aop\PointcutInterface;
use Psr\Log\LoggerInterface;

class ExceptionPointcut implements PointcutInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function matchesClass(\ReflectionClass $class)
    {
        return preg_match('/API\\\\V\d{1}\\\\(\w+)Controller$/', $class->getName());
    }

    public function matchesMethod(\ReflectionMethod $method)
    {
       return true;
    }
}