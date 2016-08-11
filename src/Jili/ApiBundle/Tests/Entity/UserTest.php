<?php
namespace Jili\ApiBundle\Tests\Entity;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\ApiBundle\Entity\User;

class UserTest extends KernelTestCase
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
        $u = new User();
        $u->setPwd('111111');

        $this->assertTrue($u->isPwdCorrect('111111'), 'correct password returns TRUE');
        $this->assertFalse($u->isPwdCorrect('111112'), 'wrong password returns FALSE');
    }

    public function test_isPasswordWenwen()
    {

        $u = new User();

        $this->assertFalse($u->isPasswordWenwen(), 'null value not wenwen password in use ');
        $u->setPasswordChoice(2);
        $this->assertFalse($u->isPasswordWenwen(), '2:  not wenwen password in use ');

        $u->setPasswordChoice(1);
        $this->assertTrue($u->isPasswordWenwen(), '1: wenwen password in use ');
    }
}

