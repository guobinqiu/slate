<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SchemeTest extends WebTestCase
{
    /**
     * @group login
     */
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
        $this->assertFalse($client->getRequest()->isSecure() );
#
        // http://{hostname}/login will redirect to https://{hostname}/login
        $crawler = $client->request('GET', 'http://localhost/login');
        $this->assertEquals(301,$client->getResponse()->getStatusCode(), 'redirect to https');
        $this->assertFalse($client->getRequest()->isSecure() );

        $client->followRedirect();
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
        $this->assertTrue($client->getRequest()->isSecure() );
    }
}
