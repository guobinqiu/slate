<?php
namespace Wenwen\AppBundle\Tests\Services;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\AppBundle\Entity\UserDeleted;

class WithdrawHandlerTest extends KernelTestCase
{
    private $em;

    public function setUp()
    {
        static::$kernel = static::createKernel(array (
            'environment' => 'test',
            'debug' => false
        ));

        static::$kernel->boot();
        $container = static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $loader = new Loader();
        $fixture = new WithdrawHandlerTestFixture();
        $loader->addFixture($fixture);

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

    /**
     */
    public function testDoWithdraw()
    {
        $container = static::$kernel->getContainer();
        $em = $this->em;

        $withdraw = $container->get('withdraw_handler');

        $users = $em->getRepository('JiliApiBundle:User')->findAll();

        $user_id = $users[0]->getId();
        $reason = 'withdraw reason';

        // user is not defined
        $return = $withdraw->doWithdraw('user_id', $reason);
        $this->assertFalse($return);

        // doWithdraw
        $return = $withdraw->doWithdraw($user_id, $reason);
        $this->assertTrue($return);
        $em->clear();

        //check data
        $user = $em->getRepository('JiliApiBundle:User')->find($user_id);
        $this->assertEmpty($user);

        //check data
        $user_deleted = $em->getRepository('WenwenAppBundle:UserDeleted')->findOneByUserId($user_id);
        $this->assertNotEmpty($user_deleted);
        $this->assertEquals('withdraw reason', $user_deleted->getReason());
        $this->assertNotEmpty($user_deleted->getCreatedAt());
        $user = unserialize($user_deleted->getUserInfo());
        $this->assertEquals('test@d8aspring.com', $user->getEmail());
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jili\ApiBundle\Entity\User;

class WithdrawHandlerTestFixture implements FixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setIsEmailConfirmed(1);
        $user->setPwd('111111');
        $manager->persist($user);
        $manager->flush();
    }
}
