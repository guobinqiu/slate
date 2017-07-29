<?php

namespace Wenwen\FrontendBundle\EventListener\API;

use Doctrine\Common\Annotations\Reader;
use Predis\Client;
use Psr\Log\LoggerInterface;
use ReflectionObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Wenwen\FrontendBundle\Controller\API\AuthenticatedController;
use Wenwen\FrontendBundle\Model\API\ApiUtils;
use Wenwen\FrontendBundle\Model\API\Status;

use Wenwen\FrontendBundle\Services\ParameterService;

class AuthenticationListener
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

        if ($controller[0] instanceof AuthenticatedController) {
            $request = $event->getRequest();

            // Serving for app level's global token
            if (!$this->authenticate($request)) {
                $event->setController(function() {
                    return new JsonResponse(ApiUtils::formatError('You are not authorized to access this api'), Status::HTTP_UNAUTHORIZED);
                });
            }

            // Serving for user level's login token
            $method = $this->getInvokedMethod($controller);
            if ($this->hasLoginTokenAnnotation($method)) {
                if (!$this->isLoginTokenValid($request)) {
                    $event->setController(function() {
                        return new JsonResponse(ApiUtils::formatError('You are not authorized to access this api'), Status::HTTP_UNAUTHORIZED);
                    });
                }
            }
        }
    }

    private function authenticate(Request $request) {
        $clientSignature = $request->headers->get(ApiUtils::HTTP_HEADER_AUTHORIZATION);
        if (!$clientSignature) {
            return false;
        }

        $appId = $this->getAppId($clientSignature);
        $appCredentials = $this->getAppCredentials($appId);
        if ($appCredentials == null) {
            return false;
        }

        $serverSignature = $this->sign($request, $appCredentials['app_id'], $appCredentials['app_secret']);
        $this->logger->debug(__METHOD__ . ' serverSignature: ' . $serverSignature);
        $this->logger->debug(__METHOD__ . ' clientSignature: ' . $clientSignature);

        // if both signatures match
        if ($serverSignature !== $clientSignature) {
            return false;
        }

        /*
         * Check API Replay Attack.
         * Per request can only use once
         */
        return $this->checkReplayAttack($request);
    }

    private function getAppId($signature) {
        return explode(':', ApiUtils::urlsafe_b64decode($signature))[0];
    }

    private function getAppCredentials($appId) {
        $apps = $this->parameterService->getParameter('api_apps');
        foreach ($apps as $app) {
            if ($app['app_id'] === $appId) {
                $this->logger->debug(__METHOD__ . '     app_id: ' . $app['app_id']);
                $this->logger->debug(__METHOD__ . ' app_secret: ' . $app['app_secret']);
                $this->logger->debug(__METHOD__ . '   app_desc: ' . $app['desc']);
                return $app;
            }
        }
        return null;
    }

    private function sign(Request $request, $appId, $appSecret) {
        /*
         * Notes the order!!!
         * Client side should concatenate request parameters in the same way
         */
        $data[0] = $request->getMethod();
        $data[1] = $request->getRequestUri();
        $data[2] = $request->headers->get(ApiUtils::HTTP_HEADER_TIMESTAMP, '');
        $data[3] = $request->headers->get(ApiUtils::HTTP_HEADER_NONCE, '');

        $message = strtolower(implode("\n", $data));
        $this->logger->debug(__METHOD__ . ' message: ' . $message);


        $digest = hash_hmac(ApiUtils::ALGO, $message, $appSecret);
        $signature = ApiUtils::urlsafe_b64encode($appId . ':' . $digest);

        return $signature;
    }

    private function checkReplayAttack(Request $request) {
        $timestamp = $request->headers->get(ApiUtils::HTTP_HEADER_TIMESTAMP);
        $nonce = $request->headers->get(ApiUtils::HTTP_HEADER_NONCE);

        if (!$timestamp || !$nonce) {
            return false;
        }

        if (abs((int)$timestamp - time()) > ApiUtils::REPLAY_ATTACK_LIVE_SECONDS) {
            return false;
        }

        if ($this->redis->exists($nonce)) {
            return false;
        } else {
            $this->redis->set($nonce, $nonce);
            $this->redis->expire($nonce, ApiUtils::REPLAY_ATTACK_LIVE_SECONDS);
        }

        return true;
    }

    private function getInvokedMethod($controller) {
        $reflectionController = new ReflectionObject($controller[0]);
        $reflectionMethod = $reflectionController->getMethod($controller[1]);
        return $reflectionMethod;
    }

    private function hasLoginTokenAnnotation(\ReflectionMethod $method) {
        $annotation = $this->annotationReader->getMethodAnnotation($method, 'Wenwen\FrontendBundle\Annotation\API\NeedLoginToken');
        return isset($annotation);
    }

    private function isLoginTokenValid(Request $request) {
        $loginToken = $request->headers->get(ApiUtils::HTTP_HEADER_LOGIN_TOKEN);
        if (!$loginToken) {
            return false;
        }

        if (!$this->redis->exists($loginToken)) {
            return false;
        }

        $this->redis->expire($loginToken, ApiUtils::LOGIN_TOKEN_LIVE_SECONDS);

        return true;
    }
}