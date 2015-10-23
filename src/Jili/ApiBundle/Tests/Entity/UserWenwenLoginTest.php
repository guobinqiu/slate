<?php
namespace Jili\ApiBundle\Tests\Entity;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\ApiBundle\Entity\UserWenwenLogin;

class  UserWenwenLoginTest extends KernelTestCase
{
    /**
    * @var \Doctrine\ORM\EntityManager
    */
    private $em;
    /**
    * {@inheritDoc}
    */
    public function setUp()
    {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();
        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $this->container = $container;
        $this->em = $em;
    }
    public function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }

    public function test_isPwdCorrect()
    {
        $u = new UserWenwenLogin();
        $u->setLoginPassword('aPaR9Ucsu4U=');
        $u->setLoginPasswordCryptType('blowfish');
        $u->setLoginPasswordSalt('★★★★★アジア事業戦略室★★★★★');

        $this->assertTrue($u->isPwdCorrect('111111'), 'correct password returns TRUE');
        $this->assertFalse($u->isPwdCorrect('111112'), 'wrong password returns FALSE');
    }
}

