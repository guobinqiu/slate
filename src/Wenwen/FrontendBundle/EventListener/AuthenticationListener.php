<?php

namespace Wenwen\FrontendBundle\EventListener;

use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Wenwen\FrontendBundle\Controller\API\AuthenticatedController;
use Wenwen\FrontendBundle\Services\ParameterService;

class AuthenticationListener
{
    const HTTP_HEADER_AUTHORIZATION = 'X-Authorization';
    const HTTP_HEADER_TIMESTAMP = 'X-Timestamp';
    const HTTP_HEADER_NONCE = 'X-Nonce';

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

        if ($controller[0] instanceof AuthenticatedController) {
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
        $clientSignature = $request->headers->get(self::HTTP_HEADER_AUTHORIZATION);
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
        return explode(':', self::urlsafe_b64decode($signature))[0];
    }

    private function getAppCredentials($appId) {
        $apps = $this->parameterService->getParameter('api')['apps'];
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
        $data[2] = $appId;
        $data[3] = $request->headers->get(self::HTTP_HEADER_TIMESTAMP, '');
        $data[4] = $request->headers->get(self::HTTP_HEADER_NONCE, '');

        $message = strtolower(implode("\n", $data));
        $this->logger->debug(__METHOD__ . ' message: ' . $message);

        $algo = 'sha256'; // algo: md5, sha1, ...
        $digest = hash_hmac($algo, $message, $appSecret);
        $signature = self::urlsafe_b64encode($appId . ':' . $digest);

        return $signature;
    }

    private function checkReplayAttack(Request $request) {
        $timestamp = $request->headers->get(self::HTTP_HEADER_TIMESTAMP);
        $nonce = $request->headers->get(self::HTTP_HEADER_NONCE);

        if (!$timestamp || !$nonce) {
            return false;
        }

        $duration = 300;

        // 允许误差5分钟
        if (abs((int)$timestamp - time()) > $duration) {
            return false;
        }

        // 5分钟内相同的请求只能被使用一次
        if ($this->redis->exists($nonce)) {
            return false;
        } else {
            $this->redis->set($nonce, $nonce);
            $this->redis->expire($nonce, $duration);
        }

        return true;
    }

    public static function urlsafe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public static function urlsafe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}