<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wenwen\FrontendBundle\Model\API\ApiUtils;

class CityControllerTest extends WebTestCase
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

    public function testGetProvinceCitiesSuccess()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'GET';
        $data[] = '/v1/provinces/1/cities';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces/1/cities',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    private function sign($message)
    {
        $appId = '19430461965976b27b6199c';
        $appSecret = '4da24648b8f1924148216cc8b49518e1';
        $digest = hash_hmac('sha256', strtolower($message), $appSecret);
        $signature = ApiUtils::urlsafe_b64encode($appId . ':' . $digest);
        return $signature;
    }
}
