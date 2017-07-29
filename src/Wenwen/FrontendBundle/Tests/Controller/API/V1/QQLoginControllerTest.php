<?php

namespace Wenwen\FrontendBundle\Tests\Controller\API\V1;

use Wenwen\FrontendBundle\Tests\Controller\API\ApiTestCase;

class QQLoginControllerTest extends ApiTestCase
{
    public function testCallbackAction()
    {
        $crawler = $this->client->request('GET', '/v1/qq/callback');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('success', $this->client->getResponse()->getContent());
        $this->assertContains('qq callback finish', $this->client->getResponse()->getContent());
    }
}
