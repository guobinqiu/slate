<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SsiProjectSurveyControllerTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    public function testCoverPageWithoutLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/ssi_project_survey/information/1');

        $this->assertRegExp('/user\/login$/', $client->getResponse()->getTargetUrl());
    }
}
