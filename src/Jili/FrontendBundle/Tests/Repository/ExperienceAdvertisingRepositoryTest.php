<?php
namespace Jili\FrontendBundle\Tests\Repository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExperienceAdvertisementRepositoryTest extends KernelTestCase {

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

    public function testGetAdvertisement() {
        $em = $this->em;
        $result = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisement();
        $this->assertEquals(2, count($result));

        $limit = 1;
        $result = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisement($limit);
        $this->assertEquals(1, count($result));
    }
}