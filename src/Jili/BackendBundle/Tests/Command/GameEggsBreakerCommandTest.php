<?php
namespace Jili\BackendBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Jili\BackendBundle\Command\GameEggsBreakerCommand;

use Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder\LoadTaobaoOrderFinishAuditData;
use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;

class GameEggsBreakerCommandTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() 
    {
        static :: $kernel = static :: createKernel(array (
            'environment' => 'test',
            'debug' => false
        ));

        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container= static :: $kernel->getContainer();

        $this->container = $container;
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
     * @group issue_537
     */
    public function testExecute() 
    {

        // prepare orders for audit finishing

        $container = $this->container;
        $em = $this->em;
        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);

        $loader = new Loader();
        $fixture = new LoadTaobaoOrderFinishAuditData();
        $loader->addFixture($fixture);

        $executor->purge();
        $executor->execute($loader->getFixtures());

        // mock the Kernel or create one depending on your needs
        $application = new Application(static::$kernel);
        $application->add(new GameEggsBreakerCommand());

        $command = $application->find('game:eggsBreaker');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'duration'=> 7,
                '--finish-orders-audit'=> true,
            )
        );

        // check results
        // no pending..
        $pending = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findBy(array(
                'auditStatus'=> GameEggsBreakerTaobaoOrder::AUDIT_STATUS_PENDING
            ));

        $this->assertEmpty($pending);
        //  user[0] 
        $user = LoadTaobaoOrderFinishAuditData::$USERS[0];       
        $info = $em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneByUserId($user->getId());


        //  user[1] 
        $user1 = LoadTaobaoOrderFinishAuditData::$USERS[1];       
        
        //  user[2] 
        $user2 = LoadTaobaoOrderFinishAuditData::$USERS[2];       
        
        //  user[3] 
        $user3 = LoadTaobaoOrderFinishAuditData::$USERS[3];       
         

        $this->assertEquals(1,1);
    }
}


