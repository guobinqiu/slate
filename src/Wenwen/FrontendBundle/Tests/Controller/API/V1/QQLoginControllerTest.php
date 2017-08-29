<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Test\Utils\ApiTestCase;
use Wenwen\FrontendBundle\Model\API\HttpStatus;

class QQLoginControllerTest extends ApiTestCase
{
    public function testCallbackAction()
    {
        $crawler = $this->client->request('POST', '/v1/qq/login?openid=123&access_token=abc');
        $this->assertEquals(HttpStatus::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
    }
}
