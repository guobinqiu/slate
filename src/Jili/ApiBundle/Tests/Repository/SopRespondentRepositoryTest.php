<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Wenwen\FrontendBundle\Entity\User;
use Jili\ApiBundle\Entity\SopRespondent;

class SopRespondentRepositoryTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;

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
        $this->em->clear();
        $this->em->close();
    }

    /**
     * @group dev-merge-ui-profile_point
     */
    public function testRetrieveByAppMid()
    {
        $dummyAppMid = 'xdfaadfdfadfasdfasd';

        $sopRespondent = new SopRespondent();
        $sopRespondent->setUserId(1);
        $sopRespondent->setAppMid($dummyAppMid);
        $sopRespondent->setAppId(44);
        $this->em->persist($sopRespondent);
        $this->em->flush();


        //测试已经存在的数据
        $sopRespondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieveByAppMid($dummyAppMid);
        $this->assertNotEmpty($sopRespondent);

        //测试不存在的数据
        $sopRespondent = $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieveByAppMid(99);
        $this->assertEmpty($sopRespondent);
    }

    /**
     * @group dev-merge-ui-sop_delivery_notification
     */
    public function testRetrieve91wenwenRecipientData()
    {

        //测试不存在的数据
        $recipientData = $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData(99);
        $this->assertEmpty($recipientData);

        $user = new User();
        $user->setNick('bb');
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setRegisterDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();

        $dummyAppMid = 'xdfaadfdfadfasdfasd';

        $sopRespondent = new SopRespondent();
        $sopRespondent->setUserId($user->getId());
        $sopRespondent->setAppMid($dummyAppMid);
        $sopRespondent->setAppId(44);
        $this->em->persist($sopRespondent);
        $this->em->flush();

        //测试已经存在的数据
        $recipientData = $this->em->getRepository('JiliApiBundle:SopRespondent')->retrieve91wenwenRecipientData($dummyAppMid);

        $this->assertNotEmpty($recipientData);

        $this->assertEquals('user@voyagegroup.com.cn', $recipientData['email']);
        $this->assertEquals('bb', $recipientData['name1']);
    }
}