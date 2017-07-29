<?php

namespace Wenwen\FrontendBundle\AOP\API;

use CG\Proxy\MethodInterceptorInterface;
use CG\Proxy\MethodInvocation;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Wenwen\FrontendBundle\Model\API\ApiUtils;
use Wenwen\FrontendBundle\Model\API\Status;

class LoginTokenInterceptor implements MethodInterceptorInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function intercept(MethodInvocation $invocation)
    {
        $this->logger->debug('Validating user login token now...');

        /*
         * Here I need to read token data from a http request
         * Fuck, how to inject a request object in symfony2.2 ?!!!
         */
        $loginToken = null;

        if ($this->isValid($loginToken)) {
            // make sure to proceed with the invocation otherwise the original
            // method will never be called
            return $invocation->proceed();
        }

        return new JsonResponse(ApiUtils::formatError('You are not authorized to access this api'), Status::HTTP_UNAUTHORIZED);
    }

    private function isValid($loginToken) {
        return true;
    }
}