<?php

namespace Wenwen\FrontendBundle\EventListener\API;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Wenwen\FrontendBundle\Annotation\API\ValidateUserAccessToken;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedController;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\Status;

use Wenwen\FrontendBundle\Services\ParameterService;

class UserAccessTokenListener
{
    private $logger;
    private $parameterService;
    private $redis;
    private $annotationReader;

    public function __construct(LoggerInterface $logger,
                                ParameterService $parameterService,
                                Client $redis,
                                Reader $annotationReader)
    {
        $this->logger = $logger;
        $this->parameterService = $parameterService;
        $this->redis = $redis;
        $this->annotationReader = $annotationReader;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof TokenAuthenticatedController) {
            $method = $this->getInvokedMethod($controller);
            if ($this->hasLoginTokenAnnotation($method)) {
                $request = $event->getRequest();
                if (!$this->authenticate($request)) {
                    $event->setController(function() {
                        return new JsonResponse(ApiUtil::formatError('You are not authorized to access this api'), Status::HTTP_UNAUTHORIZED);
                    });
                }
            }
        }
    }

    private function getInvokedMethod($controller) {
        $className = ClassUtils::getClass($controller[0]);
        $reflectionController = new \ReflectionClass($className);
        $reflectionMethod = $reflectionController->getMethod($controller[1]);
        return $reflectionMethod;
    }

    private function hasLoginTokenAnnotation(\ReflectionMethod $method) {
        $annotations = $this->annotationReader->getMethodAnnotations($method);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof ValidateUserAccessToken) {
                return true;
            }
        }
        return false;
    }

    private function authenticate(Request $request) {
        $userAccessToken = $request->headers->get(ApiUtil::HTTP_HEADER_USER_ACCESS_TOKEN);
        $this->logger->debug(__METHOD__ . ' userAccessToken: ' . $userAccessToken);

        if (!$userAccessToken) {
            return false;
        }

        if (!$this->redis->exists($userAccessToken)) {
            return false;
        }

        $this->redis->expire($userAccessToken, ApiUtil::USER_ACCESS_TOKEN_LIVE_SECONDS);

        return true;
    }
}