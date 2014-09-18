<?php
namespace Jili\ApiBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Jili\ApiBundle\Command\PointRecentCommand;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PointRecentCommandTest extends KernelTestCase
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
        static::$kernel = static::createKernel( array( 'environment'=> 'test', 'debug'=> false) );
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->em  = $em;
        $this->container = static :: $kernel->getContainer();
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
     * @group point_recent
     */
    public function testExecute()
    {


        // mock the Kernel or create one depending on your needs
        $application = new Application(static::$kernel);
        $application->add(new PointRecentCommand());

        $command = $application->find('point:recent');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                '--date'    => '2014-03-04',
            )
        );

        $this->assertEquals('write to /tmp/point_recent.cache'.PHP_EOL, $commandTester->getDisplay());
        $this->assertFileExists('/tmp/point_recent.cache' , 'point recent cache file generated not exists');
        $this->assertFileEquals(__DIR__.'/../../Resources/data/topcron_recentpoint.log.03042014', '/tmp/point_recent.cache', ' the content of point recent file updated');
    }



}
