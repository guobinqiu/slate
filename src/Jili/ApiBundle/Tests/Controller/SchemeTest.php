<?php

namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SchemeTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );

var_dump($client->getRequest()->isSecure());
die();
        $cn  = get_class($client->getRequest());
        $cm = get_class_methods($cn);
        var_dump($cn);
        print_r($cm);
        //$this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
    }
}
