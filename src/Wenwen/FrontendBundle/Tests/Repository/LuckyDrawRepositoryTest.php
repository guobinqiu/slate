<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\DataFixtures\ORM\LoadLuckyDrawData;

class LuckyDrawRepositoryTest extends KernelTestCase
{
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $loader = new Loader();
        $loader->addFixture(new LoadLuckyDrawData());

        $purger = new ORMPurger();
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testGetPrize()
    {
        $points = $this->em->getRepository('WenwenFrontendBundle:LuckyDraw')->getPrize('大奖池', 999999999999, 1);
        $this->assertEquals(0, $points);
        $points = $this->em->getRepository('WenwenFrontendBundle:LuckyDraw')->getPrize('大奖池', 999999999999, 1);
        $this->assertEquals(0, $points);
    }
}