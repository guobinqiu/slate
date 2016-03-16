<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\ApiBundle\Entity\User;

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

    /**
     * @group dev-merge-ui-profile_point
     */
    public function testRetrieveById()
    {
        $em = $this->em;

        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->insertByUser(1);

        //测试已经存在的数据
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveById($sop_respondent->getId());
        $this->assertNotEmpty($sop_respondent);

        //测试不存在的数据
        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveById(99);
        $this->assertEmpty($sop_respondent);
    }

    /**
     * @group dev-merge-ui-sop_delivery_notification
     */
    public function testRetrieve91wenwenRecipientData()
    {
        $em = $this->em;

        //测试不存在的数据
        $recipient_data = $em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData(99);
        $this->assertEmpty($recipient_data);

        $user = new User();
        $user->setNick('bb');
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setRegisterDate(new \DateTime());
        $em->persist($user);
        $em->flush();

        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->insertByUser($user->getId());

        //测试已经存在的数据
        $recipient_data = $em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData($sop_respondent->getId());

        $this->assertNotEmpty($recipient_data);

        $this->assertEquals('user@voyagegroup.com.cn', $recipient_data['email']);
        $this->assertEquals('bb', $recipient_data['name1']);
        $this->assertEquals('先生', $recipient_data['title']);
    }
}