<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class BangwoyaControllerTest extends WebTestCase {

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
     * @group debug
     */
    public function testGetInfoAction() {
        $client = static :: createClient();
        $container = $client->getContainer();

        $em = $this->em;

        $config = $container->getParameter('bangwoya_com');

        $users = LoadUserData :: $USERS;
        $user = $users[0];
        $partnerid = $user->getId();

        //验证不通过返回
        $params = array (
            'tid' => null,
            'partnerid' => null,
            'vmoney' => null,
            'nonceStr' => null
        );
        $crawler = $client->request('GET', '/api/bangwoya/getInfo?' . http_build_query($params));
        $return = $client->getResponse()->getContent();
        $this->assertEquals('{"partnerid":"","vmoney":"","tid":"","status":"no","errno":"1001"}', $return);

        //验证通过返回
        $vmoney = 100;
        $tid = 123456;
        $nonceStr = md5($config['key'] . $partnerid . $vmoney . $tid);
        $params = array (
            'tid' => $tid,
            'partnerid' => $partnerid,
            'vmoney' => $vmoney,
            'nonceStr' => $nonceStr
        );
        $crawler = $client->request('GET', '/api/bangwoya/getInfo?' . http_build_query($params));
        $return = $client->getResponse()->getContent();
        $this->assertEquals('{"partnerid":"'.$partnerid.'","vmoney":"100","tid":"123456","status":"success","sn":1}', $return);
    }
}