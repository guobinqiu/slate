<?php
namespace Jili\ApiBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Jili\ApiBundle\Command\CpaRankingActivityCommand;
use Jili\ApiBundle\Utility\FileUtil;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadSeptemberActivityData;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CpaRankingActivityCommandTest extends KernelTestCase {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel(array (
            'environment' => 'test',
            'debug' => false
        ));
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

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
     * @group point_recent
     * @group issue_437
     */
    public function testExecute() {
        $container = static :: $kernel->getContainer();

        $em = $this->em;

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadSeptemberActivityData();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $output_filename = $container->getParameter('file_path_cpa_ranking_activity');

        exec('rm -rf ' . $output_filename);
        $this->assertFileNotExists($output_filename);

        // mock the Kernel or create one depending on your needs
        $application = new Application(static :: $kernel);
        $application->add(new CpaRankingActivityCommand());

        $command = $application->find('jili:cpa_ranking_activity');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array (
            'command' => $command->getName(),
            'start_time' => '2014-08-01 00:00:00',
            'end_time' => '2014-08-31 23:59:59'
        ));

        $this->assertFileExists($output_filename, 'generate cpa ranking file');
        $users = FileUtil :: readCsvContent($filename);

        //$this->assertEquals($expected, $actual, 'compare the output file content');
    }

}