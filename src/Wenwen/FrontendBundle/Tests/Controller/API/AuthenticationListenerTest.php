<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\EventListener\AuthenticationListener;

class AuthenticationListenerTest extends WebTestCase
{
    private $container;
    private $client;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->container = self::$kernel->getContainer();
        $this->client = static::createClient(array(), array('HTTP_HOST' => 'api.91wenwen.com'));
    }

    protected function tearDown()
    {
        $this->container = null;
        $this->client = null;
    }

    public function testSignatureSuccess()
    {
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

        echo $this->client->getResponse()->getContent();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testSignatureError() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[0] = 'GET';
        $data[1] = '/v1/provinces';
        $data[2] = $timestamp;
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

    public function testGetProvinceReplayAttack() {
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

    public function testTimestampInbound()
    {
        $timestamp = time() + 200;
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

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testTimestampOutbound() {
        $timestamp = time() + 400;
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

    private function sign($message) {
        $appId = '19430461965976b27b6199c';
        $appSecret = '4da24648b8f1924148216cc8b49518e1';
        $digest = hash_hmac('sha256', strtolower($message), $appSecret);
        $signature = AuthenticationListener::urlsafe_b64encode($appId . ':' . $digest);
        return $signature;
    }
}
