<?php
namespace Jili\ApiBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Jili\ApiBundle\Command\PointRecentCommand;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;

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
     * @group debug 
     */
    public function testExecute()
    {
        $target = static::$kernel->getCacheDir().'/point_recent.cache' ;
        exec('rm -rf '.$target);
        $container = $this->container;
        $em = $this->em;

        $directory = $container->get('kernel')->getBundle('JiliApiBundle')->getPath(); 
        $directory .= '/DataFixtures/ORM/Command/PointRecent';
        $loader = new DataFixtureLoader($container);
        $loader->loadFromDirectory($directory);

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $executor->execute($loader->getFixtures());

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

        $this->assertEquals('write to '.$target.PHP_EOL, $commandTester->getDisplay());

        $this->assertFileExists($target, 'point recent cache file generated '.$target.' should exists');

        $this->assertFileEquals(__DIR__.'/../../Resources/data/topcron_recentpoint.log.03042014',$target, ' the content of point recent file updated:'.PHP_EOL.__DIR__.'/../../Resources/data/topcron_recentpoint.log.03042014 '.PHP_EOL.$target);
    }
}
