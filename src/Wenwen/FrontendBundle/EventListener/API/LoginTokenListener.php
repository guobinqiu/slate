<?php

namespace Wenwen\FrontendBundle\EventListener\API;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Wenwen\FrontendBundle\Annotation\API\NeedLogin;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedController;
use Wenwen\FrontendBundle\Controller\API\V1\UserController;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\HttpStatus;
use Wenwen\FrontendBundle\Services\ParameterService;

class LoginTokenListener
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
            if ($this->hasNeedLoginAnnotation($method)) {
                $request = $event->getRequest();
                try {
                    $this->authenticate($request);
                } catch (\Exception $e) {
                    $message = $e->getMessage();
                    $event->setController(
                        function() use ($message) {
                            return new JsonResponse(ApiUtil::formatError($message), HttpStatus::HTTP_UNAUTHORIZED);
                        }
                    );
                }
            }
        }
    }

    private function getInvokedMethod($controller) 
    {
        $className = ClassUtils::getClass($controller[0]);
        $reflectionController = new \ReflectionClass($className);
        $reflectionMethod = $reflectionController->getMethod($controller[1]);
        return $reflectionMethod;
    }

    private function hasNeedLoginAnnotation(\ReflectionMethod $method)
    {
        $annotations = $this->annotationReader->getMethodAnnotations($method);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof NeedLogin) {
                return true;
            }
        }
        return false;
    }

    private function authenticate(Request $request)
    {
        $authToken = $request->headers->get(CorsListener::X_AUTH_TOKEN);
        $this->logger->debug(__METHOD__ . ' authToken=' . $authToken);

        if (!isset($authToken)) {
            throw new \RuntimeException("Missing X_AUTH_TOKEN in request header");
        }

        if (!$this->redis->exists($authToken)) {
            throw new \RuntimeException("Invalid X_AUTH_TOKEN in request header");
        }

        //延续token登录的时长，只要半小时里有接受到请求就不会登出
        $this->redis->expire($authToken, 1800);
    }
}