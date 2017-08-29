<?php

namespace Wenwen\FrontendBundle\Tests\EventListener\API;

use Test\Utils\ApiTestCase;
use Wenwen\FrontendBundle\EventListener\API\CorsListener;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

class AccessTokenListenerTest extends ApiTestCase
{
    public function testSignatureSuccess()
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

    public function testSignatureError()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'GET';
        $data[] = '/v1/provinces';
        $data[] = $timestamp;
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
        $this->assertEquals(HttpStatus::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
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

        // send same request immediately
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

        $this->assertEquals(HttpStatus::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }

    public function testTimestampInbound()
    {
        $timestamp = time() + 300;
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

    public function testTimestampOutbound()
    {
        $timestamp = time() - 601;
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

        $this->assertEquals(HttpStatus::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }
}
