<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Test\Utils\ApiTestCase;
use Wenwen\FrontendBundle\EventListener\API\CorsListener;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

class ProvinceControllerTest extends ApiTestCase
{
    public function testGetProvincesSuccess()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'GET';
        $data[] = '/v1/provinces';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = strtoupper(implode("", $data));
        $signature = $this->createSignature($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),
            array(),
            array(
                'HTTP_' . CorsListener::X_ACCESS_TOKEN => $signature,
                'HTTP_' . CorsListener::X_TIMESTAMP => $timestamp,
                'HTTP_' . CorsListener::X_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )
        );

        $this->assertEquals(HttpStatus::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testGetAuthHeaders()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'GET';
        $data[] = '/v1/provinces';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = strtoupper(implode("", $data));
        $signature = $this->createSignature($message);

        echo PHP_EOL . CorsListener::X_ACCESS_TOKEN . '='. $signature;
        echo PHP_EOL . CorsListener::X_TIMESTAMP . '='. $timestamp;
        echo PHP_EOL . CorsListener::X_NONCE . '='. $nonce;
    }
}
