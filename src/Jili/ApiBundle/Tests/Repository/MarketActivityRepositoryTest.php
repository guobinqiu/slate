<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\MarketActivity\LoadMarketyActivityCodeData;
use Jili\ApiBundle\DataFixtures\ORM\MarketActivity\LoadAdvertisermentCodeData;

class MarketActivityRepositoryTest extends KernelTestCase
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
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $container = static::$kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        // load a Fixtures in src/Jili/FrontendBundle/DataFixtures/ORM/AutoCheckIn 
        $directory = $container->get('kernel')->getBundle('JiliApiBundle')->getPath(); 
        $directory .= '/DataFixtures/ORM/MarketActivity';
        $loader = new DataFixtureLoader($container);
        $loader->loadFromDirectory($directory);
        $executor->execute($loader->getFixtures());
        $this->em  = $em;
        $this->container  = $container;
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
     * @group issue_403
     */
    public function testNowActivity()
    {
        $em = $this->em;
        // not returns on 100 ad_id
        $return =$em->getRepository('JiliApiBundle:MarketActivity')->nowActivity(100);
        $this->assertEmpty($return,'no adid of 100 returns');

        $ad_id = LoadAdvertisermentCodeData::$ROWS[0]->getId();
        $return =$em->getRepository('JiliApiBundle:MarketActivity')->nowActivity($ad_id);
        $this->assertArrayHasKey('activityDescription',$return[0]);
        $this->assertEquals('Test Activity Description',$return[0]['activityDescription']);

        // adid is null 
        $return =$em->getRepository('JiliApiBundle:MarketActivity')->nowActivity();
        $this->assertArrayHasKey('activityDescription',$return[0]);
        $this->assertEquals('Test Activity Description',$return[0]['activityDescription']);

    }

    /**
     * @group issue_403
     */
    public function testGetActivityList()
    {
        $em = $this->em;
        $limit = 1;
        $return =$em->getRepository('JiliApiBundle:MarketActivity')->getActivityList($limit);
        $this->assertCount($limit, $return);
        $this->assertArrayHasKey('activityDescription',$return[0]);
        $this->assertEquals('Test Activity Description',$return[0]['activityDescription']);
    }

}
