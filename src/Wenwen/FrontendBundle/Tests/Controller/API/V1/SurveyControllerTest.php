<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Test\Utils\ApiTestCase;
use Wenwen\FrontendBundle\Controller\API\V1\UserController;
use Wenwen\FrontendBundle\EventListener\API\CorsListener;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

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
        $message = strtoupper(implode("", $data));
        $signature = $this->createSignature($message);

        $loginToken = $this->login()[UserController::LOGIN_TOKEN_NAME];

        $crawler = $this->client->request(
            'GET',
            '/v1/surveys',
            array(),
            array(),
            array(
                'HTTP_' . CorsListener::X_ACCESS_TOKEN => $signature,
                'HTTP_' . CorsListener::X_TIMESTAMP => $timestamp,
                'HTTP_' . CorsListener::X_NONCE => $nonce,
                'HTTP_' . CorsListener::X_LOGIN_TOKEN => $loginToken,
                'CONTENT_TYPE' => 'application/json',
            )
        );

        $this->assertEquals(HttpStatus::HTTP_OK, $this->client->getResponse()->getStatusCode());
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
        $message = strtoupper(implode("", $data));
        $signature = $this->createSignature($message);

        $loginToken = 'awronglogintoken';

        $crawler = $this->client->request(
            'GET',
            '/v1/surveys',
            array(),
            array(),
            array(
                'HTTP_' . CorsListener::X_ACCESS_TOKEN => $signature,
                'HTTP_' . CorsListener::X_TIMESTAMP => $timestamp,
                'HTTP_' . CorsListener::X_NONCE => $nonce,
                'HTTP_' . CorsListener::X_LOGIN_TOKEN => $loginToken,
                'CONTENT_TYPE' => 'application/json',
            )
        );

        $this->assertEquals(HttpStatus::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
        $this->assertContains('error', $this->client->getResponse()->getContent());
    }
}
