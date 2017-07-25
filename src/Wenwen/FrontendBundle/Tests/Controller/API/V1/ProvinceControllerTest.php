<?php
namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\EventListener\TokenListener;

class ProvinceControllerTest extends WebTestCase
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
        $this->client = static::createClient();

        $apps = $this->container->get('app.parameter_service')->getParameter('apps');
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

        $data = '';
        $data .= 'GET';
        $data .= '/api/v1/provinces';
        $data .= $this->appId;
        $data .= $timestamp;
        $data .= $nonce;

        $token = $this->generateToken($data);

        $crawler = $this->client->request(
            'GET',
            '/api/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_X-App-Access-Token' => $token,
                'HTTP_X-Timestamp' => $timestamp,
                'HTTP_X-Nonce' => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceWithoutAppId() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data = '';
        $data .= 'GET';
        $data .= '/api/v1/provinces';
        $data .= $timestamp;
        $data .= $nonce;

        $token = $this->generateToken($data);

        $crawler = $this->client->request(
            'GET',
            '/api/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_X-App-Access-Token' => $token,
                'HTTP_X-Timestamp' => $timestamp,
                'HTTP_X-Nonce' => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceWithoutTimestamp() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data = '';
        $data .= 'GET';
        $data .= '/api/v1/provinces';
        $data .= $this->appId;
        $data .= $timestamp;
        $data .= $nonce;

        $token = $this->generateToken($data);

        $crawler = $this->client->request(
            'GET',
            '/api/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_X-App-Access-Token' => $token,
                'HTTP_X-Nonce' => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceWithWrongToken() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data = '';
        $data .= 'GET';
        $data .= '/api/v1/provinces';
        $data .= $this->appId;
        $data .= $timestamp;
        $data .= $nonce;

        $token = $data;

        $crawler = $this->client->request(
            'GET',
            '/api/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_X-App-Access-Token' => $token,
                'HTTP_X-Timestamp' => $timestamp,
                'HTTP_X-Nonce' => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceReplayAttack() {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data = '';
        $data .= 'GET';
        $data .= '/api/v1/provinces';
        $data .= $this->appId;
        $data .= $timestamp;
        $data .= $nonce;

        $token = $this->generateToken($data);

        $crawler = $this->client->request(
            'GET',
            '/api/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_X-App-Access-Token' => $token,
                'HTTP_X-Timestamp' => $timestamp,
                'HTTP_X-Nonce' => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());

        // send a same request immediately
        $crawler = $this->client->request(
            'GET',
            '/api/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_X-App-Access-Token' => $token,
                'HTTP_X-Timestamp' => $timestamp,
                'HTTP_X-Nonce' => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    private function generateToken($data) {
        return base64_encode($this->appId . ':' . hash_hmac(TokenListener::ALGO, strtoupper($data), $this->appSecret));
    }
}
