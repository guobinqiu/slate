<?php

namespace Wenwen\FrontendBundle\AOP\API;

use JMS\AopBundle\Aop\PointcutInterface;
use Psr\Log\LoggerInterface;

class LoginPointcut implements PointcutInterface
{
    private $logger;
    private $annotationReader;

    public function __construct(LoggerInterface $logger, \Doctrine\Common\Annotations\Reader $annotationReader)
    {
        $this->logger = $logger;
        $this->annotationReader = $annotationReader;
    }

    public function matchesClass(\ReflectionClass $class)
    {
        return preg_match('/Controller$/', $class->getName());
    }

    public function matchesMethod(\ReflectionMethod $method)
    {
        $annotation = $this->annotationReader->getMethodAnnotation($method, 'Wenwen\FrontendBundle\Annotation\API\Login');
        return isset($annotation);
    }
}