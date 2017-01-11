<?php

namespace Wenwen\FrontendBundle\Tests\Command;

use Wenwen\FrontendBundle\Command\CheckoutSurveyListCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Wenwen\FrontendBundle\Command\GmoMemberListCSVCommand;

class CommandTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $container = static::$kernel->getContainer();
        $this->container = $container;
        $em = $container->get('doctrine')->getManager();
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }

    public function testCheckoutSurveyListCommand() {
        $application = new Application(static::$kernel);
        $application->add(new CheckoutSurveyListCommand());
        $command = $application->find('sop:checkout_survey_list');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            '--user_id' => 1,
        ));
    }

    public function testGmoMemberListCSVCommand() {
        $application = new Application(static::$kernel);
        $application->add(new GmoMemberListCSVCommand());
        $command = $application->find('gmo:member_list_csv');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
    }
}