<?php

namespace Wenwen\AppBundle\Tests\Command;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class SsiPointRewardCommandTest extends KernelTestCase
{
    protected static $kernel;

    public static function setUpBeforeClass()
    {
        static::$kernel = static::createKernel(array(
            'environment' => 'test',
            'debug' => false,
        ));
        static::$kernel->boot();
    }

    public function testExecute()
    {
        // mock the Kernel or create one depending on your needs
        $application = new Application(self::$kernel);
        $application->add(new \Wenwen\AppBundle\Command\SsiPointRewardCommand());
        $command = $application->find('panel:reward-ssi-point');
        $command->setContainer(self::$kernel->getContainer());
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));
    }
}
