<?php
namespace Jili\BackednBundle\Tests\Command;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Jili\BackendBundle\Command\ChanetCommand;

class ChanetCommandTest extends KernelTestCase {

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
        $container= static :: $kernel->getContainer();

        $this->container = $container;
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
     * @group issue_469
     */
    public function testExecute() {
        $this->markTestIncomplete('This test has not been implemented yet.');

        $container = $this->container;
        $em = $this->em;

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $path_backend= $container->get('kernel')->getBundle('JiliBackendBundle')->getPath();
        $directory = $path_backend.'/DataFixtures/ORM/Command/Chanet';

        $loader = new DataFixtureLoader($container);
        $loader->loadFromDirectory($directory);
        $executor->execute($loader->getFixtures());

        
        // Mock the ChanetHttpRequest 
        try {
            $path_yml = $path_backend.'/Tests/data/chanet_advertiserment.yml';
            $yaml = new Parser();

            $chanet_request_fixtures = $yaml->parse(file_get_contents($path_yml));

            $request_map = array();
            foreach($chanet_request_fixtures as $index => $row) {
                $request_map[$row['url'] ] = $row['return'];
            }


            $chanetHttpRequestStub = $this->getMockBuilder('Jili\BackendBundle\Services\Advertiserment\ChanetHttpRequest',array('isScriptRedirect','isExpired', 'fetch'))
                ->disableOriginalConstructor()
                ->getMock();
            $chanetHttpRequestStub->method('fetch')
                ->will($this->returnValue(true));

            $chanetHttpRequestStub->method();

        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }


        //run the command 
        // mock the Kernel or create one depending on your needs
        $application = new Application(static :: $kernel);
        $application->add(new ChanetCommand());

        $command = $application->find('advertiserment:chanet');

        $commandTester = new CommandTester($command);

        $commandParam = array (
            'command' => $command->getName(),
            '--joinCheckinAdverList' => true
        );

//        $commandTester->execute($commandParam);

        $this->assertEquals(1,1);
    
    }
}


