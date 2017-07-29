<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Wenwen\FrontendBundle\Model\API\ApiUtils;
use Wenwen\FrontendBundle\Tests\Controller\API\ApiTestCase;

class SurveyControllerTest extends ApiTestCase
{
    public function testShowSurveyListSuccess()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'GET';
        $data[] = '/v1/surveys';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $user = $this->login();
//        print_r($user);

        $loginToken = $user['login_token'];

        $crawler = $this->client->request(
            'GET',
            '/v1/surveys',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
                'HTTP_' . ApiUtils::HTTP_HEADER_LOGIN_TOKEN => $loginToken,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

    public function testShowSurveyListError()
    {
        $timestamp = time();
        $nonce = md5(uniqid(rand(), true));

        $data[] = 'GET';
        $data[] = '/v1/surveys';
        $data[] = $timestamp;
        $data[] = $nonce;
        $message = implode("\n", $data);
        $signature = $this->sign($message);

        $loginToken = 'awronglogintoken';

        $crawler = $this->client->request(
            'GET',
            '/v1/surveys',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtils::HTTP_HEADER_AUTHORIZATION => $signature,
                'HTTP_' . ApiUtils::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtils::HTTP_HEADER_NONCE => $nonce,
                'HTTP_' . ApiUtils::HTTP_HEADER_LOGIN_TOKEN => $loginToken,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }
}
