<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\EventListener\AuthenticationListener;

class AuthenticationListenerTest extends WebTestCase
{
    private $container;
    private $client;
    private $appId;
    private $appSecret;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->container = self::$kernel->getContainer();
        $parameterService = $this->container->get('app.parameter_service');
        $host = $parameterService->getParameter('api_host');
        $this->client = static::createClient(array(), array('HTTP_HOST' => $host));

        $apps = $parameterService->getParameter('api_apps');
        $app = $apps[0];
        $this->appId = $app['app_id'];
        $this->appSecret = $app['app_secret'];
    }

    protected function tearDown()
    {
        $this->container = null;
        $this->client = null;
        $this->appId = null;
        $this->appSecret = null;
    }

    public function testGetProvincesSuccess()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[0] = 'GET';
        $data[1] = '/v1/provinces';
        $data[2] = $this->appId;
        $data[3] = $timestamp;
        $data[4] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . AuthenticationListener::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceWithoutAppId() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[0] = 'GET';
        $data[1] = '/v1/provinces';
        $data[2] = $timestamp;
        $data[3] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . AuthenticationListener::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceWithoutTimestamp() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[0] = 'GET';
        $data[1] = '/v1/provinces';
        $data[2] = $this->appId;
        $data[3] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . AuthenticationListener::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceWithNullSignature() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $signature = null;

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . AuthenticationListener::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceReplayAttack() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[0] = 'GET';
        $data[1] = '/v1/provinces';
        $data[2] = $this->appId;
        $data[3] = $timestamp;
        $data[4] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . AuthenticationListener::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());

        // send a same request immediately
        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . AuthenticationListener::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceWithTimestampInbound()
    {
        $timestamp = time() + 200;
        $nonce = md5(uniqid(rand(), true));

        $data[0] = 'GET';
        $data[1] = '/v1/provinces';
        $data[2] = $this->appId;
        $data[3] = $timestamp;
        $data[4] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . AuthenticationListener::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceWithTimestampOutbound() {
        $timestamp = time() + 400;
        $nonce = md5(uniqid(rand(), true));

        $data[0] = 'GET';
        $data[1] = '/v1/provinces';
        $data[2] = $this->appId;
        $data[3] = $timestamp;
        $data[4] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . AuthenticationListener::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . AuthenticationListener::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testPrepareDataForPostman() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[0] = 'GET';
        $data[1] = '/v1/provinces';
        $data[2] = $this->appId;
        $data[3] = $timestamp;
        $data[4] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        echo PHP_EOL . 'timestamp=' . $timestamp;
        echo PHP_EOL . 'nonce=' . $nonce;
        echo PHP_EOL . 'signature=' . $signature;
    }

    private function sign($message) {
        $digest = hash_hmac('sha256', strtolower($message), $this->appSecret);
        return AuthenticationListener::urlsafe_b64encode($this->appId . ':' . $digest);
    }
}
