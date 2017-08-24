<?php

namespace Test\Utils;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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

    protected function sign($message)
    {
        $appId = '19430461965976b27b6199c';
        $appSecret = '4da24648b8f1924148216cc8b49518e1';
        $digest = hash_hmac('sha256', strtolower($message), $appSecret);
        $signature = ApiUtil::urlsafe_b64encode($appId . ':' . $digest);
        return $signature;
    }

    protected function login()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'POST';
        $data[] = '/v1/users/login';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $content = '{
            "login": {
                "username": "13916122915",
                "password": "111111"
            }
        }';

        $crawler = $this->client->request(
            'POST',
            '/v1/users/login',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            ),//server
            $content
        );

        $content = $this->client->getResponse()->getContent();

        return json_decode($content, true)['data'];
    }
}
