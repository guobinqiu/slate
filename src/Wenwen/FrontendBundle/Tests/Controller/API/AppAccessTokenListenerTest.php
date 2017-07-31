<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API;

use Wenwen\FrontendBundle\Model\API\ApiUtil;

class AppAccessTokenListenerTest extends ApiTestCase
{
    public function testSignatureSuccess()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'GET';
        $data[] = '/v1/provinces';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
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

        $data[] = 'GET';
        $data[] = '/v1/provinces';
        $data[] = $timestamp;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
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

        $data[] = 'GET';
        $data[] = '/v1/provinces';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
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

        // send a same request immediately
        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
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

        $data[] = 'GET';
        $data[] = '/v1/provinces';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
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

    public function testTimestampOutbound()
    {
        $timestamp = time() + 400;
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'GET';
        $data[] = '/v1/provinces';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'GET',
            '/v1/provinces',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }
}
