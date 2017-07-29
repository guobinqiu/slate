<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API;

use Wenwen\FrontendBundle\Model\API\ApiUtils;

class AuthenticationListenerTest extends ApiTestCase
{
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
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        echo $this->client->getResponse()->getContent();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testSignatureError()
    {
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
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testGetProvinceReplayAttack()
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
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
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
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
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
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testTimestampOutbound()
    {
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
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }
}
