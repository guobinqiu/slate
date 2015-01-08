<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Doctrine\Common\DataFixtures\Loader;


use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
//use Jili\ApiBundle\DataFixtures\ORM\Advertiserment\LoadAdCategoryData;

class ActivityGatheringCheckinLogRepositoryTest extends KernelTestCase {


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
//        $directory = $container->get('kernel')->getBundle('JiliApiBundle')->getPath();
//        $directory .= '/DataFixtures/ORM/Advertiserment';
//        $loader = new DataFixtureLoader($container);
//        $loader->loadFromDirectory($directory);
//        $executor->execute($loader->getFixtures());
        $tn  = $this->getName();
        if ($tn === 'log') {
            $loader = new Loader();
            $fixture = new LoadUserData();
            $fixture->setContainer($container);
            $loader->addFixture($fixture);
            $loader->execute($loader->getFixtures());

        }
        $this->em = $em;
        $this->container = $container;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }


    /**
     * @group issue_618
     * @group debug 
     */
    public function testLog()
    {
        $em = $this->em;
        $em->getRepository('JiliApiBundle:ActivityGatheringCheckinLog')->log(array('userId'=> LoadUserData::$USERS[0]->getId()));
        $this->assertEquals(1,'1');
    }
}

