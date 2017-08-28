<?php

namespace Wenwen\FrontendBundle\EventListener\API;

use Doctrine\Common\Annotations\Reader;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Wenwen\FrontendBundle\Controller\API\TokenAuthenticatedController;
use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Model\API\HttpStatus;
use Wenwen\FrontendBundle\Services\ParameterService;

class AppAccessTokenListener
{
    const SIGNATURE_ALGORITHM = 'sha256'; // Can be one of md5, sha1, ...
    const REPLAY_ATTACK_TTL = 600; //10min

    /*
     * base64 the 64 means a-z, A-Z, 0-9, '+', '/'
     * so delimiter is a character not in the above 64 characters
     */
    const SIGNATURE_DELIMITER = ':';

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

    private function authenticate(Request $request) 
    {
        $clientSignature = $request->headers->get(CorsListener::X_APP_ACCESS_TOKEN);
        if (!isset($clientSignature)) {
            throw new \InvalidArgumentException("Missing 'X_APP_ACCESS_TOKEN' in request header");
        }

        $timestamp = $request->headers->get(CorsListener::X_TIMESTAMP);
        if (!isset($timestamp)) {
            throw new \InvalidArgumentException("Missing 'X_TIMESTAMP' in request header");
        }

        $nonce = $request->headers->get(CorsListener::X_NONCE);
        if (!isset($nonce)) {
            throw new \InvalidArgumentException("Missing 'X_NONCE' in request header");
        }

        $appId = $this->getAppId($clientSignature);
        $this->logger->debug(__METHOD__ . ' appId=' . $appId);

        $appSecret = $this->getAppSecret($appId);
        $this->logger->debug(__METHOD__ . ' appSecret=' . $appSecret);

        $serverMessage = $this->createServerMessage($request);
        $this->logger->debug(__METHOD__ . ' serverMessage =' . $serverMessage);

        $serverSignature = $this->createServerSignature($appId, $appSecret, $serverMessage);
        $this->logger->debug(__METHOD__ . ' serverSignature=' . $serverSignature);
        $this->logger->debug(__METHOD__ . ' clientSignature=' . $clientSignature);

        if ($clientSignature !== $serverSignature) {
            throw new \RuntimeException('Client signature did not match server signature');
        }

        $this->checkReplayAttack($timestamp, $nonce);
    }

    private function getAppId($clientSignature)
    {
        $signature = ApiUtil::urlsafe_b64decode($clientSignature);
        $pos = strpos($signature, self::SIGNATURE_DELIMITER);
        if ($pos === false) {
            throw new \InvalidArgumentException("Missing '" . self::SIGNATURE_DELIMITER . "' delimiter for your signature");
        }
        return explode(self::SIGNATURE_DELIMITER, $signature)[0];
    }

    private function getAppSecret($appId) 
    {
        $apps = $this->parameterService->getParameter('api_apps');
        if (is_null($apps)) {
            throw new \InvalidArgumentException("Missing configuration option 'api_apps'");
        }
        foreach ($apps as $app) {
            if (!isset($app['app_id'])) {
                throw new \InvalidArgumentException("Missing configuration option 'app_id'");
            }
            if ($app['app_id'] === $appId) {
                if (!isset($app['app_secret'])) {
                    throw new \InvalidArgumentException("Missing configuration option 'app_secret'");
                }
                return $app['app_secret'];
            }
        }
        throw new \RuntimeException('No app_secret was found with app_id: ' . $appId);
    }

    /*
     * base64encode(appId + ":" + sha256(message, appSecret))
     */
    private function createServerSignature($appId, $appSecret, $messageToSign)
    {
        $digest = hash_hmac(self::SIGNATURE_ALGORITHM, $messageToSign, $appSecret);
        $signature = ApiUtil::urlsafe_b64encode($appId . self::SIGNATURE_DELIMITER . $digest);
        return $signature;
    }

    /*
     * Notes order!!!
     *
     * 1. method (get,post,put,delete)
     * 2. uri (pathInfo + queryString)
     * 3. body (posted json data)
     * 4. timestamp (client side current POSIX time)
     * 5. nonce (client side uuid)
     * 6. to uppercase
     */
    private function createServerMessage(Request $request)
    {
        $data[] = $request->getMethod();
        $data[] = $request->getRequestUri();
        $payload = $request->request->all();
        if (!empty($payload)) {
            $data[] = json_encode($payload);
        }
        $data[] = $request->headers->get(CorsListener::X_TIMESTAMP);
        $data[] = $request->headers->get(CorsListener::X_NONCE);
        return strtoupper(implode("", $data));
    }

    private function checkReplayAttack($timestamp, $nonce) 
    {
        $this->logger->debug(__METHOD__ . ' timestamp=' . $timestamp);
        $this->logger->debug(__METHOD__ . ' nonce=' . $nonce);

        $serverTime = time();
        if (abs($timestamp - $serverTime) > self::REPLAY_ATTACK_TTL) {
            throw new \RuntimeException('Timestamp has expired');
        }

        if ($this->redis->exists($nonce)) {
            throw new \RuntimeException('Nonce has existed');
        }

        $this->redis->set($nonce, $nonce);
        $this->redis->expire($nonce, self::REPLAY_ATTACK_TTL);
    }
}