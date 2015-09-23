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

    public function testConst()
    {
        $this->assertEquals(0,User::ORIGIN_FLAG_NEW, 'ORIGIN_FLAG_NEW = 0 ');
        $this->assertEquals(1,User::ORIGIN_FLAG_JILI, 'ORIGIN_FLAG_JILI = 1 ');
        $this->assertEquals(2,User::ORIGIN_FLAG_WENWEN, 'ORIGIN_FLAG_JILI = 2');
        $this->assertEquals(3,User::ORIGIN_FLAG_WENWEN_JILI, 'origin_flag_wenwen_jili = 3');
    }

    public function test_isOriginFlagWenwen()
    {
        $u = new User();
        $this->assertFalse($u->isOriginFlagWenwen(), 'origin_flag is null should returns false ');
        $u->setOriginFlag( 1);
        $this->assertFalse($u->isOriginFlagWenwen(), 'origin_flag is 1 should returns false ');
        $u->setOriginFlag( 2);
        $this->assertTrue($u->isOriginFlagWenwen(), 'origin_flag is 2 should returns false ');
        $u->setOriginFlag( 0);
        $this->assertFalse($u->isOriginFlagWenwen(), 'origin_flag is 0 should returns false ');
        $u->setOriginFlag( 3);
        $this->assertFalse($u->isOriginFlagWenwen(), 'origin_flag is 3 should returns false ');
    }
}

