<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Test\Utils\ApiTestCase;
use Wenwen\FrontendBundle\Model\API\ApiUtil;

class CityControllerTest extends ApiTestCase
{
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
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }
}
