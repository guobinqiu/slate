<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\Repository\UserRepository\LoadDmdeliveryData;

class SendPointFailRepositoryTest extends KernelTestCase
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
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->em  = $em;
    }
     /**
     * @group issue619
     */
    public function testGethasSendedUsers() {
        $em = $this->em;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $container = static :: $kernel->getContainer();
        // load fixtures
        $fixture = new LoadDmdeliveryData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $user_ids = array(1115);
        $type = array(173,180);
        $user = $em->getRepository('JiliApiBundle:SendPointFail')->gethasSendedUsers($user_ids,$type);
        $this->assertEquals(2, count($user));
        $this->assertEquals(1111, $user[0]['userId']);
        $this->assertEquals(1114, $user[1]['userId']);
        
        $user_ids = array(1111,1115);
        $type = array(180,173,150);
        $user = $em->getRepository('JiliApiBundle:SendPointFail')->gethasSendedUsers($user_ids,$type);
        $this->assertEquals(2, count($user));
        $this->assertEquals(1114, $user[0]['userId']);
        $this->assertEquals(1116, $user[1]['userId']);
    }
}
