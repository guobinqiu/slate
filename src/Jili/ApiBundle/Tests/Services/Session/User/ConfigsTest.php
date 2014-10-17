<?php
namespace Jili\ApiBundle\Tests\Services\Session\User;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserConfigsCheckinOpMethodCodeData;

class ConfigsTest extends KernelTestCase
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
        $container  = static::$kernel->getContainer();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserConfigsCheckinOpMethodCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

        $this->em  = $em;
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
     * @group issue_469
     */
    public function testGetCheckinOpMethod()
    {

        $em = $this->em;

        $container  = static::$kernel->getContainer();

        $userConfigs = $container->get('session.user_configs') ;

        $session = $container->get('session');

        // session  exists
        $this->assertFalse($session->has('user.checkin_op_method.alive'),'no uid ,no session even updated');

        $session->set('user.checkin_op_method.alive', 1);
        $session->save();
        
        $this->assertTrue($session->has('user.checkin_op_method.alive'));
        $this->assertEquals(1, $session->get('user.checkin_op_method.alive'));

        $session->set('user.checkin_op_method.alive', 0);
        $session->save();
        
        $this->assertTrue($session->has('user.checkin_op_method.alive'));
        $this->assertEquals(0, $session->get('user.checkin_op_method.alive'));
        // session not exists the same to testUpdateCheckinOpMethod();
        
    }

    /**
     * @group issue_469
     */
    public function testUpdateCheckinOpMethod()
    {
        $em = $this->em;

        $container  = static::$kernel->getContainer();

        $userConfigs = $container->get('session.user_configs') ;

        $session = $container->get('session');

        $this->assertNull($session->get('user.checkin_op_method.alive'),'null before update');
        $this->assertFalse($session->has('user.checkin_op_method.alive'),'no session before update');

        $this->assertNull($userConfigs->updateCheckinOpMethod(),'null as no user signed in');
        $this->assertFalse($session->has('user.checkin_op_method.alive'),'no uid ,no session even updated');

        $user = LoadUserConfigsCheckinOpMethodCodeData::$USER[0];
        $session->set('uid', $user->getId());
        $session->save();

        $this->assertNull($userConfigs->updateCheckinOpMethod(),'chiang32 without config');
        $this->assertTrue($session->has('user.checkin_op_method.alive'),'uid exist, config record is null, then session exists with value 0 ');
        $this->assertEquals(0,$session->get('user.checkin_op_method.alive'),'uid exist, config record is null, then session exists with value 0 ');

        $user = LoadUserConfigsCheckinOpMethodCodeData::$USER[1];
        $session->set('uid', $user->getId());
        $session->save();

        $this->assertEquals(1, $userConfigs->updateCheckinOpMethod(),'alice with config = 1');
        $this->assertTrue($session->has('user.checkin_op_method.alive'),'uid exist, config record exists, then session exists with value 1 ');
        $this->assertEquals(1,$session->get('user.checkin_op_method.alive'),'uid exist, config record exists, then session exists with value 1 ');

        $user = LoadUserConfigsCheckinOpMethodCodeData::$USER[2];
        $session->set('uid', $user->getId());
        $session->save();
        $this->assertEquals(0, $userConfigs->updateCheckinOpMethod(),'bob32 with config = 0');
        $this->assertTrue($session->has('user.checkin_op_method.alive'),'uid exist, config record is null, then session exists with value 0 ');
        $this->assertEquals(0,$session->get('user.checkin_op_method.alive'),'uid exist, config record is null, then session exists with value 0 ');
    }
}
