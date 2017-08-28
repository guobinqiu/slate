<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Test\Utils\ApiTestCase;
use Wenwen\FrontendBundle\EventListener\API\CorsListener;

class UserControllerTest extends ApiTestCase
{
    public function testSmsTokenSuccess()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $payload = '{"mobile_number":"13916122915"}';

        $data[] = 'POST';
        $data[] = '/v1/users/sms-token';
        $data[] = $payload;
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = strtoupper(implode("", $data));
        $signature = $this->createSignature($message);

        $crawler = $this->client->request(
            'POST',
            '/v1/users/sms-token',
            array(), //parameters
            array(), //files
            array(
                'HTTP_' . CorsListener::X_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . CorsListener::X_TIMESTAMP => $timestamp,
                'HTTP_' . CorsListener::X_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            ), //server
            $payload
        );

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testLogin()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $payload = '{"login":{"username":"13916122915","password":"111111"}}';

        $data[] = 'POST';
        $data[] = '/v1/users/login';
        $data[] = $payload;
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = strtoupper(implode("", $data));
        $signature = $this->createSignature($message);

        $crawler = $this->client->request(
            'POST',
            '/v1/users/login',
            array(),
            array(),
            array(
                'HTTP_' . CorsListener::X_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . CorsListener::X_TIMESTAMP => $timestamp,
                'HTTP_' . CorsListener::X_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            ),
            $payload
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }
}
