<?php
namespace Jili\EmarBundle\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

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
        $this->assertCount(19, $result);
    }

    /**
     * @group serchByDigit
     */
    public function testserchByLetter() {
        $em = $this->em;
        $result = $em->getRepository('JiliEmarBundle:EmarWebsitesCroned')->serchByLetter('J');
        $this->assertCount(30, $result);
    }
}