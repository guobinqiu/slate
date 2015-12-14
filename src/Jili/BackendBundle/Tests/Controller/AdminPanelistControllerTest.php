<?php

namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadMergedUserData;
use Jili\BackendBundle\Controller\AdminPanelistController;

class AdminPanelistControllerTest extends WebTestCase
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
        $fixture = new LoadMergedUserData();
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
     * @group dev-backend_panelist
     */
    public function testSearchAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        //检索默认页面
        $url = 'admin/panelist/search';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('panelsearch', $client->getResponse()->getContent());

        //click serach
        $form = $crawler->selectButton('s')->form();
        $form['panelistSerach[user_id]'] = 31;
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('zhangmm@ec-navi.com.cn"', $client->getResponse()->getContent());
    }

    /**
     * @group dev-backend_panelist
     */
    public function testEditPanelist()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        //沒有id
        $url = 'admin/panelist/edit';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('No such panelist_id', $client->getResponse()->getContent());

        //有id
        $url = $container->get('router')->generate('_admin_panelist_edit', array (
            'id' => 31
        ));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('/admin/panelist/editConfirm', $client->getResponse()->getContent());

        //提交到确认页面
        $form = $crawler->selectButton('Confirm')->form();
        $form['user[nick]'] = 'I am nick';
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('/admin/panelist/editCommit', $client->getResponse()->getContent());

        //完成编辑
        $form = $crawler->selectButton('Confirm')->form();
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Update completed!', $client->getResponse()->getContent());
    }

    /**
     * @group dev-backend_panelist
     */
    public function testPointHistoryAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        //沒有id
        $url = 'admin/panelist/pointHistory';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('No such panelist_id', $client->getResponse()->getContent());

        //有id
        $url = $container->get('router')->generate('_admin_panelist_pointhistory', array (
            'id' => 31
        ));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('zhangmm@ec-navi.com.cn', $client->getResponse()->getContent());
        $this->assertContains('2014-03-04 00:19:06', $client->getResponse()->getContent());
        $this->assertContains('211', $client->getResponse()->getContent());
        $this->assertContains('60', $client->getResponse()->getContent());
        $this->assertContains('游戏全勤', $client->getResponse()->getContent());
    }

    /**
     * @group dev-backend_panelist
     */
    public function testGetUserHobbyName()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $controller = new AdminPanelistController();
        $controller->setContainer($container);

        $user_hobby = null;
        $user_hobby_name = $controller->getUserHobbyName($user_hobby);
        $this->assertEquals('', $user_hobby_name);

        $user_hobby = '';
        $user_hobby_name = $controller->getUserHobbyName($user_hobby);
        $this->assertEquals('', $user_hobby_name);

        $user_hobby = '1';
        $user_hobby_name = $controller->getUserHobbyName($user_hobby);
        $this->assertEquals('上网', $user_hobby_name);

        $user_hobby = '1,2,13';
        $user_hobby_name = $controller->getUserHobbyName($user_hobby);
        $this->assertEquals('上网,音乐', $user_hobby_name);
    }
}
