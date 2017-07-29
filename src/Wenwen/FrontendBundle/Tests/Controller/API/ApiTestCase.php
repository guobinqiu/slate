<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\Model\API\ApiUtils;

class ApiTestCase extends WebTestCase
{
    protected $container;
    protected $client;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->container = self::$kernel->getContainer();
        $this->client = static::createClient(array(), array('HTTP_HOST' => 'api.91wenwen.com'));
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
        $signature = ApiUtils::urlsafe_b64encode($appId . ':' . $digest);
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
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            ),//server
            $content
        );

        $content = $this->client->getResponse()->getContent();

        return json_decode($content, true)['data']['user'];
    }
}
