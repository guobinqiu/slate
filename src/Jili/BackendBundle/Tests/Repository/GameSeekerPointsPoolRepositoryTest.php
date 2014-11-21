<?php
namespace  Jili\BackendBundle\Tests\Repository;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
#use Doctrine\Common\DataFixtures\Loader;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
class    GameSeekerPointsPoolRepositoryTest extends KernelTestCase
{

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
        
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
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
     * @group issue-524
     * @group debug 
     */
    public function testbatchInsertRules()
    {
        $em = $this->em;
        $rules =<<<EOD
1:500
2:100
4:50
10:20
40:5
100:2
500:0
EOD;
        $em->getRepository('JiliBackendBundle:GameSeekerPointsPool')->batchInsertRules($rules);
        $this->assertNotNull($em->getRepository('JiliBackendBundle:GameSeekerPointsPool')->findOneBy(array(
            'points'=> 500,
            'sendFrequency' 1,
            'isValid'=> 0,
            'isPublished'=> 0,
        )));

    }
}
