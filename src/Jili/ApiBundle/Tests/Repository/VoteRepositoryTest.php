<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadVoteData;

class VoteRepositoryTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;
    private $user;

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
    public function testFetchVoteList()
    {
        $em = $this->em;
        $voteList = $em->getRepository('JiliApiBundle:Vote')->fetchVoteList();
        $this->assertEquals(1, count($voteList), 'active_flag:true, the count of vote is' . count($voteList));
        $this->assertEquals(1, $voteList[0]['id'], 'default vote id is ' . $voteList[0]['id']);
        $voteList = $em->getRepository('JiliApiBundle:Vote')->fetchVoteList(false);
        $this->assertEquals(1, count($voteList), 'active_flag:false, the count of vote is ' . count($voteList));
        $this->assertEquals(2, $voteList[0]['id'], 'default vote id is ' . $voteList[0]['id']);
    }
}