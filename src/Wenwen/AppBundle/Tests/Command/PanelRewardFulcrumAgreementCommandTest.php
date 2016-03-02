<?php

namespace Wenwen\AppBundle\Tests\Command;

use Phake;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Wenwen\AppBundle\DataFixtures\ORM\LoadRewardFulcrumCommandData;

use Wenwen\AppBundle\Command\Wenwen\AppBundle\Command;
use Wenwen\AppBundle\Command\PanelRewardFulcrumAgreementCommand;

class PanelRewardFulcrumAgreementCommandTest extends KernelTestCase
{
  /**
   *      * @var \Doctrine\ORM\EntityManager
   *           */
  private $em;
  private $sop_responednt;
  /**
   *      * {@inheritDoc}
   *           */
  public function setUp()
  {
    static::$kernel = static::createKernel(array (
      'environment' => 'test',
      'debug' => false
    ));
    static::$kernel->boot();
    $container = static::$kernel->getContainer();
    $em = $container->get('doctrine')->getManager();

    // purge tables
    $purger = new ORMPurger($em);
    $executor = new ORMExecutor($em, $purger);
    $executor->purge();
    // load fixtures
    $fixture = new LoadRewardFulcrumCommandData();
    $loader = new Loader();
    $loader->addFixture($fixture);
    $executor->execute($loader->getFixtures());

    $this->em = $em;
    $this->container = $container;
  }

  /**
   * {@inheritDoc}
   */
  protected function tearDown()
  {
    parent::tearDown();
    $this->em->close();
    Phake::resetStaticInfo();
  }

  public function testExecuteInvalidAppMid()
  {
    $em = $this->em;
    $container = $this->container;
    $application = new Application(static::$kernel);
    $application->add(new PanelRewardFulcrumAgreementCommand());
    $command = $application->find('panel:reward-fulcrum-agreement');
    $this->assertInstanceOf('\Wenwen\AppBundle\Command\PanelRewardFulcrumAgreementCommand', $command, 'wrong class type');
    $command->setContainer($container);
    $commandTester = new CommandTester($command);
  }

  public function testInvalidAppMID()
  {}

  public function testUpdateTable()
  {}

  public static function getPointHistory()
  {}

}

?>
