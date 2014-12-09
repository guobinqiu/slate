<?php
namespace Jili\BackendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;
use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;


use Doctrine\Common\DataFixtures\Loader;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder\LoadTaobaoOrdersData;

class GameEggsBreakerTaobaoOrderRepoisitoryTest extends KernelTestCase
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
        $tn = $this->getName();
        if($tn === 'testUpdateOne' ) {
            $fixture = new LoadTaobaoOrdersData();
            $loader  = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }


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
     * #group debug 
     */
    public function testInsertUserPost() 
    {
        $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->insertUserPost(array('userId'=> 1, 'orderPaid'=> 100.01, 'orderId'=> '10d93jasdf0f2' ));

        $expected_entity = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy( array('userId'=> 1,
                'orderId'=>'10d93jasdf0f2',
                'orderPaid'=> 100.01));
        $this->assertNotNull($expected_entity);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerTaobaoOrder',$expected_entity);

        // invalid testing
    }

    /**
     * @group issue_537 
     * @group debug 
     */
    public function testUpdateOneOnAudit() 
    {
        // auditing 

        // ng 

        // valid 

        // uncertain

        $this->assertEquals(1,1);
    }


}
