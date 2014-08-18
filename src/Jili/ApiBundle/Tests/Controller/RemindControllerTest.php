<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RemindControllerTest extends WebTestCase
{
    public function testremindTotalPointAction()
    {
        $client = static :: createClient();
        $container = $client->getContainer();
        $upper_limit = $container->getParameter('point_exchange_upper_limit');
        $this->assertEquals(2000, $upper_limit);
    }
}
