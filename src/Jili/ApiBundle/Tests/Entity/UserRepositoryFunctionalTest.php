<?php
namespace Jili\ApiBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;

use Jili\ApiBundle\DataFixtures\ORM\Repository\UserRepository\LoadUserData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserCpaPointsCodeData;

class UserRepositoryFunctionalTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        $tn = $this->getName();
        if (in_array($tn, array('testGetTotalCPAPointsByTime', 'testGetUserCPAPointsByTime') )) {

            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();
            if (in_array($tn, array('testGetTotalCPAPointsByTime') )) {
                $directory = $container->get('kernel')->getBundle('JiliApiBundle')->getPath(); 
                $directory .= '/DataFixtures/ORM/Repository/UserRepository';
                $loader = new DataFixtureLoader($container);
                $loader->loadFromDirectory($directory);
                $executor->execute($loader->getFixtures());
            } else if (in_array($tn, array( 'testGetUserCPAPointsByTime') )) {
                $fixture = new LoadUserCpaPointsCodeData(); 
                $fixture->setContainer($container);
                $loader = new Loader();
                $loader->addFixture($fixture);
                $executor->execute($loader->getFixtures());

            }
        }
        $this->container = $container;
        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }
    /**
     * @group cpa_points
     * @group debug
     */
    public function testGetUserCPAPointsByTime()
    {
        $start = '2014-07-01 00:00:00';
        $end = '2014-07-31 23:59:59';
        $user_id = 1173775;
        $myInfo = $this->em->getRepository('JiliApiBundle:User')->getUserCPAPointsByTime($start, $end, $user_id);
        $this->assertEquals(1000, $myInfo[0]['points']);
    }

    /**
     * @group cpa_points 
     */
    public function testGetTotalCPAPointsByTime()
    {
        $start = '2014-07-01 00:00:00';
        $end = '2014-07-31 23:59:59';

        //前100名
        $limit = 100;
        $offset = 0;
        $users = $this->em->getRepository('JiliApiBundle:User')->getTotalCPAPointsByTime($start, $end, $limit, $offset);
        $this->assertEquals(100, count($users));
    }
}
