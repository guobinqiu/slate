<?php
namespace Wenwen\AppBundle\Tests\Services;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

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
        $user_withdraw = $em->getRepository('WenwenAppBundle:UserWithdraw')->findOneByUserId($user_id);
        $this->assertNotEmpty($user_withdraw);
        $this->assertEquals('withdraw reason', $user_withdraw->getReason());
        $this->assertNotEmpty($user_withdraw->getCreatedAt());

        $user_deleted = $em->getRepository('WenwenAppBundle:UserDeleted')->find($user_id);
        $this->assertNotEmpty($user_deleted);

        $user_wenwen_login_deleted = $em->getRepository('WenwenAppBundle:UserWenwenLoginDeleted')->findOneByUserId($user_id);
        $this->assertNotEmpty($user_wenwen_login_deleted);
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

        $login = new \Jili\ApiBundle\Entity\UserWenwenLogin();
        $login->setUser($user);
        $login->setLoginPassword('aPaR9Ucsu4U=');
        $login->setLoginPasswordCryptType('blowfish');
        $login->setLoginPasswordSalt('★★★★★アジア事業戦略室★★★★★');
        $manager->persist($login);
        $manager->flush();
    }
}
