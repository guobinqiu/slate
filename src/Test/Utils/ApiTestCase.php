<?php

namespace Test\Utils;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\EventListener\API\AppAccessTokenListener;
use Wenwen\FrontendBundle\EventListener\API\CorsListener;
use Wenwen\FrontendBundle\Model\API\ApiUtil;

class ApiTestCase extends WebTestCase
{
    protected $container;
    protected $client;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->container = self::$kernel->getContainer();
        $this->client = static::createClient();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->container = null;
        $this->client = null;
    }

    protected function createSignature($message)
    {
        $appId = '19430461965976b27b6199c';
        $appSecret = '4da24648b8f1924148216cc8b49518e1';
        $digest = hash_hmac(AppAccessTokenListener::SIGNATURE_ALGORITHM, $message, $appSecret);
        $signature = ApiUtil::urlsafe_b64encode($appId . AppAccessTokenListener::SIGNATURE_DELIMITER . $digest);
        return $signature;
    }

    protected function login()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $payload = '{"login":{"username":"13916122915","password":"111111"}}';

        $data[] = 'POST';
        $data[] = '/v1/users/login';
        $data[] = $payload;
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = strtoupper(implode("", $data));
        $signature = $this->createSignature($message);

        $crawler = $this->client->request(
            'POST',
            '/v1/users/login',
            array(),
            array(),
            array(
                'HTTP_' . CorsListener::X_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . CorsListener::X_TIMESTAMP => $timestamp,
                'HTTP_' . CorsListener::X_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            ),
            $payload
        );

        $content = $this->client->getResponse()->getContent();
        return json_decode($content, true)['data'];
    }
}
