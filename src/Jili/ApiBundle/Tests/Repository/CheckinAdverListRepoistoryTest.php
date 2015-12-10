<?php
namespace Jili\ApiBundle\Tests\Repository;


use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Jili\ApiBundle\Entity\CheckinAdverList;

class CheckinAdverListRepositoryTest extends KernelTestCase {

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
        $container = static :: $kernel->getContainer();
        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $directory = $container->get('kernel')->getBundle('JiliApiBundle')->getPath();
        $directory .= '/DataFixtures/ORM/Repository/CheckinAdverList';
        $loader = new DataFixtureLoader($container);
        $loader->loadFromDirectory($directory);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;

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
     * @group issue_469
     */
    public function testShowCheckinList()
    {
        $em = $this->em;
        $uid = 105;

        $advertiserments = $em->getRepository('JiliApiBundle:CheckinAdverList')->showCheckinList($uid, '2015-01-01'); 
        $this->assertCount(6, $advertiserments,'no limit method on checkin');
        $this->assertEquals('7e3953ab0100441f49803e71cc6fe78e', md5(json_encode($advertiserments) ));
        $directory = $this->container->get('kernel')->getBundle('JiliApiBundle')->getPath();
        $expected = $directory. '/Resources/data/show_checkinlist_expected.json';
        $this->assertJsonStringEqualsJsonFile( $expected,  json_encode($advertiserments) );

    }
}
