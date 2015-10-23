<?php

namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
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

        @session_start();
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
        $this->assertEquals(1, $result['pagination']->count());
        $item = $result['pagination']->getItems();
        $this->assertEquals(1, $item[0]['id']);

        $result = $controller->getVoteList(1, false);
        $this->assertEquals(1, $result['pagination']->count());
        $item = $result['pagination']->getItems();
        $this->assertEquals(2, $item[0]['id']);
    }

    /**
     * @group admin_vote
     */
    public function testCreateVote()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = '/admin/vote/edit';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('create')->form();
        // set some values
        $form['vote[title]'] = '【生活】堪比人参的养生食物，你知道吗？';
        $form['vote[description]'] = '人参是深受人们喜爱的补品之一，具有很好的保健养生功效，人参价值贵，使人望而却步。然而你知道吗?其实生活中有很多食物功效可与“人参”相媲美，一下的养生圣品，你知道哪个？';
        $form['answer_number_1'] = '动物人参——鹌鹑';
        $form['answer_number_2'] = '果蔬人参——胡萝卜';

        // submit the form
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('commit')->form();
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("設問を追加しました。")')->count());

        //check vote create success
        $vote = $em->getRepository('JiliApiBundle:Vote')->findOneByTitle('【生活】堪比人参的养生食物，你知道吗？');
        $this->assertNotNull($vote);

        $this->assertEquals('人参是深受人们喜爱的补品之一，具有很好的保健养生功效，人参价值贵，使人望而却步。然而你知道吗?其实生活中有很多食物功效可与“人参”相媲美，一下的养生圣品，你知道哪个？', $vote->getDescription());
        $this->assertEquals(1, $vote->getPointValue());
        $this->assertEquals(date('Y-m-d') . ' 00:00:00', $vote->getStartTime()->format('Y-m-d H:i:s'));
        $this->assertEquals(date('Y-m-d') . ' 23:59:59', $vote->getEndTime()->format('Y-m-d H:i:s'));
        $stashData = $vote->getStashData();
        $this->assertEquals('动物人参——鹌鹑', $stashData['choices'][1]);
        $this->assertEquals('果蔬人参——胡萝卜', $stashData['choices'][2]);
    }

    /**
     * @group admin_vote
     */
    public function testEditVote()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = '/admin/vote/edit';
        $crawler = $client->request('GET', $url, array (
            'id' => 1
        ));

        $before_vote = $em->getRepository('JiliApiBundle:Vote')->findOneById(1);
        $this->assertEquals('【生活】英语九大前缀 你认识哪个？', $before_vote->getTitle());

        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('create')->form();
        // set some values
        $form['vote[title]'] = '【生活】英语九大前缀的认识';
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('commit')->form();
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("設問の設定変更をしました。")')->count());

        //check vote edit success
        $em->clear();
        $after_vote = $em->getRepository('JiliApiBundle:Vote')->findOneById(1);
        $this->assertEquals('【生活】英语九大前缀的认识', $after_vote->getTitle());
    }

    /**
     * @group admin_vote
     */
    public function testDeleteAction()
    {
        $client = static::createClient();
        $session = $client->getContainer()->get('session');

        $em = $this->em;

        $csrfProvider = new DefaultCsrfProvider('SECRET');
        $csrf_token = $csrfProvider->generateCsrfToken('vote');
        $session->set('csrf_token', $csrf_token);
        $session->save();

        $this->assertTrue($session->has('csrf_token'));

        $before = $em->getRepository('JiliApiBundle:Vote')->findOneById(1);
        $this->assertNotNull($before);

        $url = '/admin/vote/delete';
        $crawler = $client->request('GET', $url, array (
            'id' => 1,
            'page' => '',
            'ret_action' => '',
            'csrf_token' => $csrf_token
        ));
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $after = $em->getRepository('JiliApiBundle:Vote')->findOneById(1);
        $this->assertNull($after);
    }
}
