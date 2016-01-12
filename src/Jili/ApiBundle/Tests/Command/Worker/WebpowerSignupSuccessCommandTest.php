<?php
namespace Jili\ApiBundle\Tests\Command\Worker;

use Jili\ApiBundle\Command\Worker\WebpowerSignupSuccessCommand;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use JMS\JobQueueBundle\Entity\Job;

class WebpowerSignupSuccessCommandTest extends KernelTestCase
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

    public function testExecuteJob() 
    {
        // mock the Kernel or create one depending on your needs
        $em = $this->em;
        $application = new Application(static::$kernel);
        $application->add(new WebpowerSignupSuccessCommand());

        $command = $application->find('webpower-mailer:signup-success');
        $commandTester = new CommandTester($command);

        $args = array( '--campaign_id=1','--group_id=83','--mailing_id=2411','--email=tao.jiang@d8aspring.com','--title=注册成功test','--name=江');

        $job = new Job('webpower-mailer:signup-success',$args,  true, '91wenwen_signup');
        $em->persist($job);
        $em->flush($job);

        $jobs =  $em->getRepository('JMSJobQueueBundle:Job')->findAll();
        $this->assertCount(1, $jobs, 'only 1 job ' );
        $job=$jobs[0];
        $this->assertEquals(Job::STATE_PENDING,$job->getState() ,'pending');
        $this->assertEquals('webpower-mailer:signup-success',$job->getCommand() ,'the comand ');
        $this->assertEquals('91wenwen_signup',$job->getQueue() ,'the queue');
        $this->assertEquals($args ,$job->getArgs() ,'pending');
    }

}
