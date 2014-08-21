<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jili\FrontendBundle\Controller\HomeController;

class HomeControllerTest extends WebTestCase
{
    /**
     * @group vote
     **/
    public function testVoteAction()
    {
        //todo assert the session config. reduce the configuration on gc_lifetime.
        $client = static::createClient();
        $container = $client->getContainer();

        $crawler = $client->request('GET', '/home/vote');
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("快速问答")')->count()
        );
    }
}
