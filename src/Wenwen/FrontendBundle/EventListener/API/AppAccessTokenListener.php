<?php

namespace Wenwen\FrontendBundle\EventListener\API;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\ClassUtils;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Wenwen\FrontendBundle\Annotation\API\NeedLoginToken;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedController;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\Status;

use Wenwen\FrontendBundle\Services\ParameterService;

class AppAccessTokenListener
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
            $request = $event->getRequest();
            if (!$this->authenticate($request)) {
                $event->setController(function() {
                    return new JsonResponse(ApiUtil::formatError('You are not authorized to access this api'), Status::HTTP_UNAUTHORIZED);
                });
            }
        }
    }

    private function authenticate(Request $request) {
        $clientSignature = $request->headers->get(ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN);
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
        return explode(':', ApiUtil::urlsafe_b64decode($signature))[0];
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
        $data[] = $request->getMethod();
        $data[] = $request->getRequestUri();
        $data[] = $request->headers->get(ApiUtil::HTTP_HEADER_TIMESTAMP, '');
        $data[] = $request->headers->get(ApiUtil::HTTP_HEADER_NONCE, '');

        $message = strtolower(implode("\n", $data));
        $this->logger->debug(__METHOD__ . ' message: ' . $message);


        $digest = hash_hmac(ApiUtil::ALGO, $message, $appSecret);
        $signature = ApiUtil::urlsafe_b64encode($appId . ':' . $digest);

        return $signature;
    }

    private function checkReplayAttack(Request $request) {
        $timestamp = $request->headers->get(ApiUtil::HTTP_HEADER_TIMESTAMP);
        $nonce = $request->headers->get(ApiUtil::HTTP_HEADER_NONCE);

        if (!$timestamp || !$nonce) {
            return false;
        }

        if (abs((int)$timestamp - time()) > ApiUtil::REPLAY_ATTACK_LIVE_SECONDS) {
            return false;
        }

        if ($this->redis->exists($nonce)) {
            return false;
        } else {
            $this->redis->set($nonce, $nonce);
            $this->redis->expire($nonce, ApiUtil::REPLAY_ATTACK_LIVE_SECONDS);
        }

        return true;
    }
}