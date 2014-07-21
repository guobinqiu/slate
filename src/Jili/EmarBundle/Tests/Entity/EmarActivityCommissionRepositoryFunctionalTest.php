<?php
namespace Jili\EmarBundle\Tests\Entity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmarActivityCommissionRepositoryFunctionalTest extends WebTestCase
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
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();

        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }

    public function testgetCommissionListByMallName()
    {
        $mallName = "京东";
        $commissionList = $this->em->getRepository('JiliEmarBundle:EmarActivityCommission')->getCommissionListByMallName($mallName);
        $this->assertEquals(27, count($commissionList));

        $mallName = "京东XX";
        $commissionList = $this->em->getRepository('JiliEmarBundle:EmarActivityCommission')->getCommissionListByMallName($mallName);
        $this->assertEquals(0, count($commissionList));
    }
}
