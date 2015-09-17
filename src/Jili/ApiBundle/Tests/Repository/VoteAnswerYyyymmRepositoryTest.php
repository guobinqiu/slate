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
        //$this->em->close();
    }

    /**
     * @group admin_vote
     */
    public function testGetTableNameByYyyymm()
    {
        $em = $this->em;
        $tablename = $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->getTableNameByYyyymm('201508');
        $this->assertEquals('vote_answer_201508', $tablename);
    }

    /**
     * @group admin_vote
     */
    public function testCreateYyyymmTable()
    {
        $em = $this->em;
        $result = $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->createYyyymmTable('201508');
        var_dump($result);
        //         $this->assertEquals('vote_answer_201509', $tablename);
    }

    /**
     * @group admin_vote
     */
    public function testGetAnswerCount()
    {
        $em = $this->em;
        $count = $em->getRepository('JiliApiBundle:VoteAnswerYyyymm')->getAnswerCount(1, '201508');
        var_dump($result);
        //         $this->assertEquals('vote_answer_201509', $tablename);
    }
}
