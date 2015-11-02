<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadVoteData;

class VoteAnswerRepositoryTest extends KernelTestCase
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
     * @group user_vote
     */
    public function testGetAnswerCount()
    {
        $em = $this->em;
        $count = $em->getRepository('JiliApiBundle:VoteAnswer')->getAnswerCount(1);
        $this->assertEquals(2, $count, 'vote_id:1, AnswerCount is ' . $count);
    }

    /**
     * @group user_vote
     */
    public function testGetEachAnswerCount()
    {
        $em = $this->em;
        $count = $em->getRepository('JiliApiBundle:VoteAnswer')->getEachAnswerCount(1, 1);
        $this->assertEquals(2, $count, 'vote_id:1, answer_number:1, count is ' . $count);
    }

    /**
     * @group user_vote
     */
    public function testGetUserAnswerCount()
    {
        $em = $this->em;
        $count = $em->getRepository('JiliApiBundle:VoteAnswer')->getUserAnswerCount(1, 1);
        $this->assertEquals(1, $count, 'user_id: 1 , vote_id :1 user answer count is ' . $count);
    }
}
