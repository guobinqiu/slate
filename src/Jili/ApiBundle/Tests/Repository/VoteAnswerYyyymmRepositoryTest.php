<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadVoteData;

class VoteAnswerYyyymmRepositoryTest extends KernelTestCase
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
    public function testGetTableNameByYyyymm()
    {
        $em = $this->em;
        $tablename = $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->getTableNameByYyyymm('201508');
        $this->assertEquals('vote_answer_201508', $tablename, 'table name is ' . $tablename);
    }

    /**
     * @group admin_vote
     */
    public function testCreateYyyymmTable()
    {
        $em = $this->em;
        $result = $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->createYyyymmTable('201509');
        $this->assertTrue($result, 'create table success');
    }

    /**
     * @group admin_vote
     */
    public function testGetAnswerCount()
    {
        $em = $this->em;
        $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->createYyyymmTable('201508');
        $count = $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->getAnswerCount(1, '201508');
        $this->assertEquals(2, $count, 'AnswerCount is ' . $count);
    }
}
