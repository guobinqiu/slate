<?php

namespace Wenwen\FrontendBundle\AOP\API;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Wenwen\FrontendBundle\Model\API\ApiUtils;
use Wenwen\FrontendBundle\Model\API\Status;

class ExceptionInterceptor implements MethodInterceptorInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function intercept(MethodInvocation $invocation)
    {
        try {
            // make sure to proceed with the invocation otherwise the original
            // method will never be called
            return $invocation->proceed();

        } catch (\Exception $ex) {

            $this->logger->error(__METHOD__ . ' ' . $ex->getMessage());

            return new JsonResponse(ApiUtils::formatError($ex->getMessage()), Status::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}