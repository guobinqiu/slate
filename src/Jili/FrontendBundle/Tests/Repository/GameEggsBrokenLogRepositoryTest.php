<?php
namespace Jili\BackendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;

class GameEggsBrokenLogRepoisitoryTest extends KernelTestCase
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
     * @group issue_537 
     */
    public function testAddLog() 
    {
        $this->em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')
            ->addLog(array('userId'=> 1, 'eggType'=> GameEggsBreakerEggsInfo::EGG_TYPE_COMMON , 'points'=>5 ));

        $expected_entity = $this->em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')
            ->findOneBy( array('userId'=> 1,
                'eggType'=> GameEggsBreakerEggsInfo::EGG_TYPE_COMMON,
                'pointsAcquried'=> 5));
        $this->assertNotNull($expected_entity);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBrokenLog',$expected_entity);

    }
}
