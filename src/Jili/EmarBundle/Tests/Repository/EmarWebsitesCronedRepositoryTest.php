<?php
namespace Jili\EmarBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\EmarBundle\DataFixtures\ORM\LoadEmarWebsitesCronedData;

class EmarWebsitesCronedRepositoryTest extends KernelTestCase {

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
        $container = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadEmarWebsitesCronedData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

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
     * @group serchByDigit
     */
    public function testserchByDigit() {
        $em = $this->em;
        $result = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->serchByDigit();
        $this->assertCount(1, $result);
    }

    /**
     * @group serchByLetter
     */
    public function testserchByLetter() {
        $em = $this->em;
        $result = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->serchByLetter('Y');
        $this->assertCount(1, $result);
    }
}
