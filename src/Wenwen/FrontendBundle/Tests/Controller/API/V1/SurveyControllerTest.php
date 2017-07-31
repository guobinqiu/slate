<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Wenwen\FrontendBundle\Model\API\ApiUtil;
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

        $data = $this->login();
//        print_r($data['user']);

        $userAccessToken = $data['user_access_token'];

        $crawler = $this->client->request(
            'GET',
            '/v1/surveys',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
                'HTTP_' . ApiUtil::HTTP_HEADER_USER_ACCESS_TOKEN => $userAccessToken,
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

        $userAccessToken = 'awronglogintoken';

        $crawler = $this->client->request(
            'GET',
            '/v1/surveys',
            array(),//parameters
            array(),//files
            array(
                'HTTP_' . ApiUtil::HTTP_HEADER_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . ApiUtil::HTTP_HEADER_TIMESTAMP => $timestamp,
                'HTTP_' . ApiUtil::HTTP_HEADER_NONCE => $nonce,
                'HTTP_' . ApiUtil::HTTP_HEADER_USER_ACCESS_TOKEN => $userAccessToken,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }
}
