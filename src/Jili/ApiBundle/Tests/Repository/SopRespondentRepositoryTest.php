<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Wenwen\FrontendBundle\Entity\User;

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
        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->insertByUser(1, 27);

        $this->assertEquals(1, $sopRespondent->getUserId());
        $this->assertEquals(1, $sopRespondent->getStatusFlag());
        $this->assertEquals(27, $sopRespondent->getAppId());

        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->insertByUser(2, 92);
        $this->assertEquals(2, $sopRespondent->getUserId());
        $this->assertEquals(1, $sopRespondent->getStatusFlag());
        $this->assertEquals(92, $sopRespondent->getAppId());
    }

    /**
     * @group dev-merge-ui-survey-list
     */
    public function testRetrieveOrInsertByUserId()
    {
        $em = $this->em;

        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->insertByUser(1, 27);

        //测试已经存在的数据
        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId(1, 27);
        $this->assertEquals(1, $sopRespondent->getUserId());
        $this->assertEquals(1, $sopRespondent->getStatusFlag());
        $this->assertEquals(27, $sopRespondent->getAppId());

        //测试不存在的数据
        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId(2, 92);
        $this->assertEquals(2, $sopRespondent->getUserId());
        $this->assertEquals(1, $sopRespondent->getStatusFlag());
        $this->assertEquals(92, $sopRespondent->getAppId());
    }

    /**
     * @group dev-merge-ui-profile_point
     */
    public function testRetrieveByAppMid()
    {
        $em = $this->em;

        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->insertByUser(1, 27);

        //测试已经存在的数据
        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveByAppMid($sopRespondent->getAppMid());
        $this->assertNotEmpty($sopRespondent);

        //测试不存在的数据
        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveByAppMid(99);
        $this->assertEmpty($sopRespondent);
    }

    /**
     * @group dev-merge-ui-sop_delivery_notification
     */
    public function testRetrieve91wenwenRecipientData()
    {
        $em = $this->em;

        //测试不存在的数据
        $recipientData = $em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData(99);
        $this->assertEmpty($recipientData);

        $user = new User();
        $user->setNick('bb');
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setRegisterDate(new \DateTime());
        $em->persist($user);
        $em->flush();

        $sopRespondent = $em->getRepository('JiliApiBundle:SopRespondent')->insertByUser($user->getId(), 27);

        //测试已经存在的数据
        $recipientData = $em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData($sopRespondent->getAppMid());

        $this->assertNotEmpty($recipientData);

        $this->assertEquals('user@voyagegroup.com.cn', $recipientData['email']);
        $this->assertEquals('bb', $recipientData['name1']);
    }
}