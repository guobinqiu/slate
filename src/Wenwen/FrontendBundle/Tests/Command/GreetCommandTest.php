<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Wenwen\FrontendBundle\Command\GreetCommand;

class GreetCommandTest extends WebTestCase {

    public function setUp(){
        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    public function testExecute(){
        $application = new Application(static::$kernel);
        $application->add(new GreetCommand());

        $command = $application->find('demo:greet');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'name' => 'Guobin',
            '--uppercase' => true
        ));

        $this->assertEquals("HELLO GUOBIN", $commandTester->getDisplay());
    }
}