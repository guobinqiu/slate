<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class AdvertisermentControllerTest extends WebTestCase {

    /**
      * @var \Doctrine\ORM\EntityManager
      */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $contianer = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new LoadUserData();
        $fixture->setContainer($contianer);

        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->container = $contianer;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_578
     */
    public function testBangwoyaAction() {
        $client = static :: createClient();
        $container = static :: $kernel->getContainer();

        $session = $container->get('session');
        $em = $this->em;
        $user_id = LoadUserData :: $USERS[0]->getId();

        $day = date('Ymd');

        //test UserAdvertisermentVisit
        $visit1 = $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->getAdvertisermentVisit($user_id, $day);
        $this->assertCount(0, $visit1);

        //login 前
        $url = '/advertiserment/bangwoya';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //login 后
        $session->set('uid', $user_id);
        $session->save();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //test UserAdvertisermentVisit
        $visit2 = $em->getRepository('JiliApiBundle:UserAdvertisermentVisit')->getAdvertisermentVisit($user_id, $day);
        $this->assertCount(1, $visit2);
    }
}