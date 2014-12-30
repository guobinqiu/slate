<?php
namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\Repository\PointHistory\LoadIssetInsertData;

class PointManageControllerTests extends WebTestCase {

    /**
     * @group issue_600
     */
    public function testPointHistorySearch() {
        $client = static :: createClient();
        $container = $client->getContainer();
        $url = '/admin/pointmanage/pointHistorySearch';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}