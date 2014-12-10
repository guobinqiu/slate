<?php
namespace Jili\BackendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
class GameEggsBreakerEggsInfoRepoisitoryTest extends KernelTestCase
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

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
//        $tn = $this->getName();


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

    /**
     * @group issue_537 
     */
    public function testInsertUserPost() 
    {
        $entity = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerEggsInfo')
            ->findOneOrCreateByUserId(1);

        $this->assertEquals(1,1);
    }
}
