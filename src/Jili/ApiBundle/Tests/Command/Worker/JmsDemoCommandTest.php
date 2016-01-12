<?php
namespace Jili\ApiBundle\Tests\Command\Worker;

use Jili\ApiBundle\Command\Worker\JmsDemoCommand;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use JMS\JobQueueBundle\Entity\Job;

class WebpowerCommandTest extends KernelTestCase
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
     */
    public function testExecute() 
    {
        $this->markTestSkipped('This is a command for research JmsQueueBundle');
        // mock the Kernel or create one depending on your needs
        $em = $this->em;
        $application = new Application(static :: $kernel);
        $application->add(new JmsDemoCommand());

        $command = $application->find('jms:demo');
        $commandTester = new CommandTester($command);
        $i=0;
        while($i < 10 )  {
            $i++;
            foreach(range('a','z') as $alpha) {
                $job = new Job('jms:demo', array( '-Q q_'.$alpha, '-t '. $i), true, 'q_'.$alpha);
                $em->persist($job);
                $em->flush($job);
            }
        }
    }
}
