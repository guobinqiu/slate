<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadVoteData;
use Jili\FrontendBundle\Controller\VoteController;
use Jili\ApiBundle\Entity\Vote;

class VoteControllerTest extends WebTestCase
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
     * @group user_vote
     * @group user_vote_ui
     */
    public function testIsInBonusHour()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $controller = new VoteController();
        $controller->setContainer($container);

        $time = date_create(date('Y-m-d'));
        $return = $controller->isInBonusHour($time);
        $this->assertTrue($return);
    }

    /**
     * @group user_vote_ui
     */
    public function testGetBonusTimeLimitDt()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $controller = new VoteController();
        $controller->setContainer($container);

        $time = date_create('2015-12-08 16:51:00');
        $bonusTime = $controller->getBonusTimeLimitDt($time);
        $this->assertEquals('2015-12-09 16:51:00', $bonusTime->format('Y-m-d H:i:s'));
    }

    /**
     * @group user_vote
     */
    public function testCalcRewardPoint()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $controller = new VoteController();
        $controller->setContainer($container);

        $vote_point = 1;
        $time = date_create(date('Y-m-d'));
        $vote_point = $controller->calcRewardPoint($vote_point, $time);
        $this->assertEquals(2, $vote_point);
    }

    /**
     * @group user_vote_ui
     */
    public function testTopAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/vote/top';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group user_vote
     * @group user_vote_ui
     */
    public function testIndexAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/vote/index';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group user_vote
     */
    public function testShowAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = '/vote/show';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/error$/', $client->getResponse()->headers->get('location'), 'need id');

        $url = '/vote/show?id=4';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/error$/', $client->getResponse()->headers->get('location'), 'vote not exist');

        $url = '/vote/show?id=2';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/error$/', $client->getResponse()->headers->get('location'), 'vote not start');

        $url = '/vote/show?id=1';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/result\?id=1$/', $client->getResponse()->headers->get('location'), 'vote has end');

        $url = '/vote/show?id=3';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $session = $client->getRequest()->getSession();
        $session->set('uid', 1);
        $session->save();

        $url = '/vote/show?id=3';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'show vote success');

        $form = $crawler->filter('form[id=show_form]')->form();
        $form['answer_number'] = 1;
        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/result\?id=3$/', $client->getResponse()->headers->get('location'), 'commit success');
    }

    /**
     * @group user_vote
     */
    public function testVoteAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $url = '/vote/vote';

        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/index$/', $client->getResponse()->headers->get('location'), 'need POST');

        $crawler = $client->request('POST', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/user\/login$/', $client->getResponse()->headers->get('location'), 'need login');

        $session = $client->getRequest()->getSession();
        $session->set('uid', 1);
        $session->save();

        $crawler = $client->request('POST', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/index$/', $client->getResponse()->headers->get('location'), 'need vote_id');

        $crawler = $client->request('POST', $url, array (
            'id' => 3
        ));
        $this->assertRegExp('/\/vote\/show\?id=3$/', $client->getResponse()->headers->get('location'), 'need answer_number');

        $crawler = $client->request('POST', $url, array (
            'id' => 3,
            'answer_number' => 1
        ));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/result\?id=3$/', $client->getResponse()->headers->get('location'), 'check csrf_tokent');

        $csrfProvider = new DefaultCsrfProvider('SECRET');
        $csrf_token = $csrfProvider->generateCsrfToken('vote');
        $session->set('csrf_token', $csrf_token);
        $session->save();

        $crawler = $client->request('POST', $url, array (
            'id' => 4,
            'answer_number' => 1,
            'csrf_token' => $csrf_token
        ));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/index$/', $client->getResponse()->headers->get('location'), 'vote not exist');

        $crawler = $client->request('POST', $url, array (
            'id' => 3,
            'answer_number' => 6,
            'csrf_token' => $csrf_token
        ));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/show\?id=3$/', $client->getResponse()->headers->get('location'), 'invalid answer_number');

        $crawler = $client->request('POST', $url, array (
            'id' => 1,
            'answer_number' => 1,
            'csrf_token' => $csrf_token
        ));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/result\?id=1$/', $client->getResponse()->headers->get('location'), 'user has answered');

        $crawler = $client->request('POST', $url, array (
            'id' => 3,
            'answer_number' => 1,
            'csrf_token' => $csrf_token
        ));
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/result\?id=3$/', $client->getResponse()->headers->get('location'), 'answer success');

        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $voteAnswer = $em->getRepository('JiliApiBundle:VoteAnswer')->findBy(array (
            'userId' => 1,
            'voteId' => 3
        ));
        $this->assertEquals(1, count($voteAnswer));

        $taskHistory = $em->getRepository('JiliApiBundle:TaskHistory01')->findBy(array (
            'userId' => 1,
            'categoryType' => 93
        ));
        $this->assertEquals(1, count($taskHistory));

        $pointHistory = $em->getRepository('JiliApiBundle:PointHistory01')->findBy(array (
            'userId' => 1,
            'reason' => 93
        ));
        $this->assertEquals(1, count($pointHistory));

        $user = $em->getRepository('JiliApiBundle:User')->find(1);
        $this->assertEquals(1, $user->getPoints());
    }

    /**
     * @group user_vote
     */
    public function testResultAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/vote/result';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/error$/', $client->getResponse()->headers->get('location'), 'need id');

        $url = '/vote/result?id=4';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/error$/', $client->getResponse()->headers->get('location'), 'vote not exist');

        $url = '/vote/result?id=2';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/error$/', $client->getResponse()->headers->get('location'), 'vote not start');

        $url = '/vote/result?id=1';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group user_vote
     */
    public function testSuggestAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $url = '/vote/suggest';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/user\/login$/', $client->getResponse()->headers->get('location'), 'need login');

        $session = $client->getRequest()->getSession();
        $session->set('uid', 1);
        $session->save();

        $url = '/vote/suggest';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertLessThan(1, $crawler->filter('html:contains("您的题目已经提交，等待审核！")')->count());

        $form = $crawler->filter('form[id=suggest_form]')->form();

        // set some values
        $form['voteSuggest[title]'] = '中国古代十大名画你知道哪一个？';
        $form['voteSuggest[description]'] = '《中国十大传世名画》皆为历代不二至宝，高头巨帙，历经磨难流传有序。那么中国古代十大名画你知道哪一个？';
        $form['voteSuggest[option1]'] = '唐代•韩滉《五牛图》';
        $form['voteSuggest[option2]'] = '清代•郎世宁《百骏图》';
        $form['voteSuggest[option3]'] = '明代•仇英《汉宫春晓图》';

        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/\/vote\/suggest\?send_ok=1$/', $client->getResponse()->headers->get('location'), 'need id');
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
