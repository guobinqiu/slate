<?php

namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadVoteData;
use Jili\BackendBundle\Controller\AdminVoteController;
use Jili\ApiBundle\Entity\Vote;

class AdminVoteControllerTest extends WebTestCase
{
    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadVoteData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
     * @group admin_vote
     */
    public function testGetTmpImageDir()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $controller = new AdminVoteController();
        $controller->setContainer($container);

        $path = $controller->getTmpImageDir();
        $this->assertNotNull($path);
    }

    /**
     * @group admin_vote
     */
    public function testIndexAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/admin/vote/index';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group admin_vote
     */
    public function testActiveListAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/admin/vote/activeList/1';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group admin_vote
     */
    public function testReserveListAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/admin/vote/reserveList/1';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group admin_vote
     */
    public function testGetVoteList()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $controller = new AdminVoteController();
        $controller->setContainer($container);

        $result = $controller->getVoteList(1, true);

        $this->assertEquals(2, $result['pagination']->count());
        $item = $result['pagination']->getItems();
        $this->assertEquals(2, $item[0]['id']);

        $result = $controller->getVoteList(1, false);
        $this->assertEquals(0, $result['pagination']->count());
    }

    /**
     * @group admin_vote
     */
    public function testEditAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/admin/vote/edit';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group admin_vote
     */
    public function testEditConfirmAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/admin/vote/editConfirm';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group admin_vote
     */
    public function testEditCommitAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/admin/vote/editCommit';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group admin_vote
     * @group mmzhang
     */
    public function testDeleteAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $before = $em->getRepository('JiliApiBundle:Vote')->findOneById(1);
        $this->assertNotNull($before);

        $url = '/admin/vote/delete';
        $crawler = $client->request('GET', $url, array (
            'id' => 1,
            'page' => '',
            'ret_action' => ''
        ));
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $after = $em->getRepository('JiliApiBundle:Vote')->findOneById(1);
        $this->assertNull($after);
    }

    /**
     * @group admin_vote
     */
    public function testGenerateMonthlyTable()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $controller = new AdminVoteController();
        $controller->setContainer($container);

        $vote = new Vote();
        $vote->setYyyymm('201509');

        $return = $controller->generateMonthlyTable($vote);
        $this->assertTrue($return);
    }
}
