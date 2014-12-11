<?php
namespace Jili\ApiBundle\Tests\Command;
use Jili\ApiBundle\Command\DmdeliveryCommand;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Jili\ApiBundle\DataFixtures\ORM\Repository\UserRepository\LoadDmdeliveryData;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DmdeliveryCommandTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        
        // load fixtures
        $container = static :: $kernel->getContainer();
        $fixture = new LoadDmdeliveryData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());
        
        $this->container = static :: $kernel->getContainer();
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue548
     */
    public function testExecute() {
        // mock the Kernel or create one depending on your needs
        $em = $this->em;
        $application = new Application(static :: $kernel);
        $application->add(new DmdeliveryCommand());

        $command = $application->find('jili:run_crontab_Dmdelivery');
        $commandTester = new CommandTester($command);
        $commandParam = array ('command' => $command->getName(),'batch_name' => 'pointFailure');
        $commandTester->execute($commandParam);
        
        $user = $em->getRepository('JiliApiBundle:User')->find(1110);
        $this->assertEquals(0, $user->getPoints());
    }
}
