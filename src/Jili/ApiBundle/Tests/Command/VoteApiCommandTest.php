<?php
namespace Jili\ApiBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Jili\ApiBundle\Command\VoteApiCommand;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VoteApiCommandTest extends KernelTestCase
{
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
     * @group issue_437
     */
    public function testExecute() {
        $container = $this->container ;
        $output_filename = $container->getParameter('file_path_wenwen_vote');
        $wenwen_vote_api = $container->getParameter('wenwen_vote_api');

        exec('rm -rf ' . $output_filename);
        $this->assertFileNotExists($output_filename);

        // mock the Kernel or create one depending on your needs
        $application = new Application(static :: $kernel);
        $application->add(new VoteApiCommand());

        $command = $application->find('jili:vote_api');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array (
            'command' => $command->getName()
        ));

        $this->assertFileExists($output_filename, 'generate vote file');

        // build the expected content with fputcsv()
        $content = file_get_contents($wenwen_vote_api);
        $rows = json_decode($content, true);
        $fh = fopen('php://memory', 'r+');
        fwrite($fh, json_encode($rows['data']));
        rewind($fh);
        $expected = fread($fh, 8096);
        fclose($fh);

        $actual = file_get_contents($output_filename);
        $this->assertEquals($expected, $actual, 'compare the output file content');
    }

}
