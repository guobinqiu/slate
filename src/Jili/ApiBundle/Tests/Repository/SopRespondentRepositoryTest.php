<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class SopRespondentRepositoryTest extends KernelTestCase
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
     * @group dev-merge-ui-survey-list
     */
    public function testInsertByUser()
    {
        $em = $this->em;
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->insertByUser(1);

        $this->assertEquals(1, $sop_respondent->getUserId());
        $this->assertEquals(1, $sop_respondent->getStatusFlag());
    }

    /**
     * @group dev-merge-ui-survey-list
     */
    public function testRetrieveOrInsertByUserId()
    {
        $em = $this->em;

        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->insertByUser(1);

        //测试已经存在的数据
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId(1);
        $this->assertEquals(1, $sop_respondent->getUserId());
        $this->assertEquals(1, $sop_respondent->getStatusFlag());

        //测试不存在的数据
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId(2);
        $this->assertEquals(2, $sop_respondent->getUserId());
        $this->assertEquals(1, $sop_respondent->getStatusFlag());
    }
}