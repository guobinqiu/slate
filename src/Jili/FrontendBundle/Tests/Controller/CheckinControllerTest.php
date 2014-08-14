<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CheckinControllerTest extends WebTestCase
{
    public function testSavecheckindata()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/saveCheckinData');
    }

    public function testAddccheckinbonus()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/addCCheckinBonus');
    }

    public function testCheckinlistforauto()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/checkinListForAuto');
    }

    public function testAutocheckinlist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/autoCheckinList');
    }

    public function testUpdateautocheckinlist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/updateAutoCheckinList');
    }

}
