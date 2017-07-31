<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Wenwen\FrontendBundle\Model\API\ApiUtil;
use Wenwen\FrontendBundle\Tests\Controller\API\ApiTestCase;

class UserControllerTest extends ApiTestCase
{
    public function testSmsTokenSuccess()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'POST';
        $data[] = '/v1/users/sms-token';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $crawler = $this->client->request(
            'POST',
            '/v1/users/sms-token',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            ),//server
            '{ "mobile_number": "13916122915" }'
        );

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testLogin()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'POST';
        $data[] = '/v1/users/login';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $content = '{
            "login": {
                "username": "13916122915",
                "password": "111111"
            }
        }';

        $crawler = $this->client->request(
            'POST',
            '/v1/users/login',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
                'CONTENT_TYPE' => 'application/json',
            ),//server
            $content
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }
}
