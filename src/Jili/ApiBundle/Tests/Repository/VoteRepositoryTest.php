<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadVoteData;
use Jili\ApiBundle\Entity\VoteAnswer;

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
     * @group user_vote
     * @group user_vote_ui
     */
    public function testFetchVoteList()
    {
        $em = $this->em;
        $voteList = $em->getRepository('JiliApiBundle:Vote')->fetchVoteList(true);
        $this->assertEquals(2, count($voteList), 'active_flag:true, the count of vote is' . count($voteList));
        $this->assertEquals(3, $voteList[0]['id'], 'vote id is ' . $voteList[0]['id']);
        $voteList = $em->getRepository('JiliApiBundle:Vote')->fetchVoteList(false);
        $this->assertEquals(1, count($voteList), 'active_flag:false, the count of vote is ' . count($voteList));
        $this->assertEquals(2, $voteList[0]['id'], 'vote id is ' . $voteList[0]['id']);
    }

    /**
     * @group survey_list_vote
     * @group survey_list_vote_fix_bug
     */
    public function testRetrieveUnanswered()
    {
        $em = $this->em;
        //1. 传参为空，默认取所有的正在进行中的快速问答
        $voteList = $em->getRepository('JiliApiBundle:Vote')->retrieveUnanswered();
        $this->assertEquals(1, count($voteList), 'user_id:null, acount of vote list: ' . count($voteList));
        //2. 传参不为空，取该用户还没有回答过的正在进行中的快速问答
        $voteList = $em->getRepository('JiliApiBundle:Vote')->retrieveUnanswered(1);
        $this->assertEquals(1, count($voteList), 'user_id:1, acount of vote list: ' . count($voteList));

        //insert vote answer
        $answer = new VoteAnswer();
        $answer->setUserId(1);
        $answer->setVoteId($voteList[0]['id']);
        $answer->setAnswerNumber(1);
        $em->persist($answer);
        $em->flush();

        $voteList = $em->getRepository('JiliApiBundle:Vote')->retrieveUnanswered(1);
        $this->assertEquals(0, count($voteList), 'user_id:1, acount of vote list: ' . count($voteList));
    }
}
