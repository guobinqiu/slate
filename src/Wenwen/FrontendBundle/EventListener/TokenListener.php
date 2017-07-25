<?php

namespace Wenwen\FrontendBundle\EventListener;

use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedController;
use Wenwen\FrontendBundle\Services\ParameterService;

class TokenListener
{
    const DURATION = 300;
    const ALGO = 'sha256';

    private $logger;
    private $parameterService;
    private $redis;

    public function __construct(LoggerInterface $logger, ParameterService $parameterService, Client $redis)
    {
        $this->logger = $logger;
        $this->parameterService = $parameterService;
        $this->redis = $redis;
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
                    return new JsonResponse(array(
                        'status' => 'error',
                        'message' => 'You are not authorized to access this api'
                    ), 401);
                });
            }
        }
    }

    private function authenticate(Request $request) {
        $token = $request->headers->get('X-App-Access-Token');
        if (!$token || ($token && strlen($token) != 120)) {
            return false;
        }

        $appId = explode(':', base64_decode($token))[0];
        $appCredentials = $this->getAppCredentials($appId);
        if ($appCredentials == null) {
            return false;
        }

        $digest = $this->signature($request, $appCredentials['app_id'], $appCredentials['app_secret']);
        $this->logger->debug(__METHOD__ . ' -- digest: ' . $digest);
        $this->logger->debug(__METHOD__ . ' --  token: ' . $token);
        if ($digest !== $token) {
            return false;
        }

        /*
         * API Replay Attack Check
         * Per request can only use once
         */
        return $this->replayAttackCheck($request);
    }

    private function getAppCredentials($appId) {
        $apps = $this->parameterService->getParameter('apps');
        foreach ($apps as $app) {
            if ($app['app_id'] === $appId) {
                $this->logger->debug($app['app_id']);
                $this->logger->debug($app['app_secret']);
                $this->logger->debug($app['desc']);
                return $app;
            }
        }
        return null;
    }

    private function signature(Request $request, $appId, $appSecret) {
        /*
         * Notes order!!!
         * Client side should follow the same sequence
         */
        $data = '';
        $data .= $request->getMethod();
        $data .= $request->getRequestUri();
        $data .= $appId;
        $data .= $request->headers->get('X-Timestamp');
        $data .= $request->headers->get('X-Nonce');
        $this->logger->debug(__METHOD__ . ' -- data: ' . $data);

        $hash = hash_hmac(self::ALGO, strtoupper($data), $appSecret);
        $digest = base64_encode($appId . ':' . $hash);

        return $digest;
    }

    private function replayAttackCheck(Request $request) {
        $timestamp = $request->headers->get('X-Timestamp');
        $nonce = $request->headers->get('X-Nonce');
        if (!$timestamp || !$nonce) {
            return false;
        }

        // 允许误差5分钟
        if (abs((int)$timestamp - time()) > self::DURATION) {
            return false;
        }

        // 5分钟内相同的请求只能被使用一次
        if ($this->redis->exists($nonce)) {
            return false;
        } else {
            $this->redis->set($nonce, $nonce);
            $this->redis->expire($nonce, self::DURATION);
        }

        return true;
    }
}