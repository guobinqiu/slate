<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\LoadExperienceAdvertisementCodeData;

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
        $container  = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $fixture = new LoadExperienceAdvertisementCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->purge();
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
     * @group advertiserment 
     */
    public function testGetAdvertisement() {

        $em = $this->em;
        $result = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisement();
        $this->assertEquals(3, count($result));

        $limit = 1;
        $result = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisement($limit);
        $this->assertEquals(1, count($result));
    }
    /**
     * @group issue430
     */
    public function testGetAdvertisementList(){
        //created 4 recodes in table before test , 1 recode's delete_flag is true.
        $em = $this->em;
        $result = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisementList();
        $this->assertEquals(3, count($result));

        $limit = 1;
        $result = $em->getRepository('JiliFrontendBundle:ExperienceAdvertisement')->getAdvertisementList($limit);
        $this->assertEquals(1, count($result));
    }
}
