<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Test\Utils\ApiTestCase;
use Wenwen\FrontendBundle\EventListener\API\CorsListener;

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
        $message = implode("", $data);
        $signature = $this->createSignature($message);

        $userAccessToken = $this->login()['user_access_token'];

        $crawler = $this->client->request(
            'GET',
            '/v1/surveys',
            array(), //parameters
            array(), //files
            array(
                'HTTP_' . CorsListener::X_APP_ACCESS_TOKEN => $signature,
                'HTTP_' . CorsListener::X_TIMESTAMP => $timestamp,
                'HTTP_' . CorsListener::X_NONCE => $nonce,
                'HTTP_' . CorsListener::X_USER_ACCESS_TOKEN => $userAccessToken,
                'CONTENT_TYPE' => 'application/json',
            )//server
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }

//    public function testShowSurveyListError()
//    {
//        $timestamp = time();
//        $nonce = md5(uniqid(rand(), true));
//
//        $data[] = 'GET';
//        $data[] = '/v1/surveys';
//        $data[] = $timestamp;
//        $data[] = $nonce;
//        $message = implode("", $data);
//        $signature = $this->createSignature($message);
//
//        $userAccessToken = 'awronglogintoken';
//
//        $crawler = $this->client->request(
//            'GET',
//            '/v1/surveys',
//            array(), //parameters
//            array(), //files
//            array(
//                'HTTP_' . CorsListener::X_APP_ACCESS_TOKEN => $signature,
//                'HTTP_' . CorsListener::X_TIMESTAMP => $timestamp,
//                'HTTP_' . CorsListener::X_NONCE => $nonce,
//                'HTTP_' . CorsListener::X_USER_ACCESS_TOKEN => $userAccessToken,
//                'CONTENT_TYPE' => 'application/json',
//            )//server
//        );
//
//        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
//        $this->assertContains('error', $this->client->getResponse()->getContent());
//    }
}
