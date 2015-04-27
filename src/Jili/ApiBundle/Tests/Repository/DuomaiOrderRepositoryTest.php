<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Jili\ApiBundle\DataFixtures\ORM\Repository\DuomaiOrder\LoadInitData;
use Jili\ApiBundle\DataFixtures\ORM\Repository\DuomaiOrder\LoadConfirmedData;

class DuomaiOrderRepositoryTest extends KernelTestCase 
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
        $container = static :: $kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $tn  = $this->getName();
        if (in_array($tn, array('testUpdateConfirmed','testUpdateInvalid'))) {
            $fixture = new LoadInitData();
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        } elseif(in_array($tn , array('testUpdateBalanced'))) {
            $fixture = new LoadConfirmedData();
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }

        

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
     * @group issue_680 
     */
    public function testInit() 
    {
        $em = $this->em;
        $params = array( 'userId'=> 105,
            'adsId'=>61,
            'adsName'=>'京东商城CPS推广',
            'siteId'=>'152244',
            'linkId'=>'0',
            'orderSn'=>'9152050154',
            'ordersPrice'=>'799.00',
            'orderTime'=> \DateTime::createFromFormat('Y-m-d H:i:s', '2014-04-20 00:00:00'),
            'ocd' => '71440050',
            'commission'=> '5.40',
        );

        $return =$em->getRepository('JiliApiBundle:DuomaiOrder')
            ->init($params);
        $this->assertInstanceOf('\Jili\ApiBundle\Entity\DuomaiOrder', $return);
        $this->assertNotNull($return->getId());
        $this->assertEquals(1, $return->getStatus(),'init order status should be 1');
    }

    /**
     * @group issue_680 
     */
    public function testUpdateConfirmed() 
    {
        $em = $this->em;
        $params = array( 
            'userId'=> 105,
            'adsId'=>61,
            'siteId'=>'152244',
            'linkId'=>'0',
            'orderSn'=>'9152050154',
            'ordersPrice'=>'799.00',
            'orderTime'=> \DateTime::createFromFormat('Y-m-d H:i:s', '2015-04-27 10:28:59'),
            'ocd' => '71440050',
            'commission'=> '5.40',
            'status'=> 2
        );
        $params['confirmedAt'] = new \DateTime();
        $return =$em->getRepository('JiliApiBundle:DuomaiOrder')
            ->update($params);

        $this->assertNotNull( $return);

        $this->assertEquals(1, $return);
        $duomai_order_stm =   $em->getConnection()->prepare('select * from duomai_order');
        $duomai_order_stm->execute();
        $duomai_order_records =$duomai_order_stm->fetchAll();

        $this->assertCount(1, $duomai_order_records);
        $this->assertEquals('71440050', $duomai_order_records[0]['ocd']);

        $this->assertEquals('2', $duomai_order_records[0]['status']);
        $this->assertEquals(5.40, $duomai_order_records[0]['comm']);
    }

    /**
     * @group issue_680
     */
    public function testUpdateBalanced() 
    {
        $em = $this->em;
        $params = array( 
            'userId'=> 105,
            'adsId'=>61,
            'siteId'=>'152244',
            'linkId'=>'0',
            'orderSn'=>'9152050154',
            'ordersPrice'=>'799.00',
            'orderTime'=> \DateTime::createFromFormat('Y-m-d H:i:s', '2015-04-27 10:28:59'),
            'ocd' => '71440050',
            'commission'=> '5.40',
            'status'=> 3,
            'statusPrevous'=> 2
        );
        $params['balancedAt'] = new \DateTime();
        $return =$em->getRepository('JiliApiBundle:DuomaiOrder')
            ->update($params);

        $this->assertNotNull( $return);
        $this->assertEquals(1, $return);

        $duomai_order_stm =   $em->getConnection()->prepare('select * from duomai_order');
        $duomai_order_stm->execute();
        $duomai_order_records =$duomai_order_stm->fetchAll();

        $this->assertCount(1, $duomai_order_records);
        $this->assertEquals('71440050', $duomai_order_records[0]['ocd']);

        $this->assertEquals('3', $duomai_order_records[0]['status']);
        $this->assertEquals(5.40, $duomai_order_records[0]['comm']);
    }

    /**
     * @group issue_680
     */
    public function testUpdateInvalid() 
    {
        $em = $this->em;
        $params = array( 
            'userId'=> 105,
            'adsId'=>61,
            'siteId'=>'152244',
            'linkId'=>'0',
            'orderSn'=>'9152050154',
            'ordersPrice'=>'799.00',
            'orderTime'=> \DateTime::createFromFormat('Y-m-d H:i:s', '2015-04-27 10:28:59'),
            'ocd' => '71440050',
            'commission'=> '5.40',
            'status'=> 4
        );
        $params['deactivatedAt'] = new \DateTime();
        $return =$em->getRepository('JiliApiBundle:DuomaiOrder')
            ->update($params);

        $this->assertNotNull( $return);

        $this->assertEquals(1, $return);
        $duomai_order_stm =   $em->getConnection()->prepare('select * from duomai_order');
        $duomai_order_stm->execute();
        $duomai_order_records =$duomai_order_stm->fetchAll();

        $this->assertCount(1, $duomai_order_records);
        $this->assertEquals('71440050', $duomai_order_records[0]['ocd']);

        $this->assertEquals('4', $duomai_order_records[0]['status']);
        $this->assertEquals(5.40, $duomai_order_records[0]['comm']);
    }

}
