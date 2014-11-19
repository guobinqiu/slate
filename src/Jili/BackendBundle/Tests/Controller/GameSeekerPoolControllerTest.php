<?php

namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameSeekerPoolControllerTest extends WebTestCase
{
    public function testBuild()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/build');
    }

    public function testPublish()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/publish');
    }

    public function testEnable()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/enable');
    }

    public function testMonitor()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/monitor');
    }

    public function testAdjust()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/adjust');
    }

}
