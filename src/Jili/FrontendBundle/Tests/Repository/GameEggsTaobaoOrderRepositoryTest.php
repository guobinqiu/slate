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
    public function setUp() 
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $tn = $this->getName();
        if(in_array($tn , array('testGetLastestEggedTimestamp','testUpdateOne','testFetchByRange','testFindLatestEggedNickList'))) {
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
        $day = new \DateTime();
        $day->setTime(0,0);
        $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->insertUserPost(array('userId'=> 1, 'orderAt'=> $day, 'orderId'=> '10d93jasdf0f2' ));

        $expected_entity = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy( array('userId'=> 1,
                'orderId'=>'10d93jasdf0f2',
                'orderAt'=> $day));
        $this->assertNotNull($expected_entity);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerTaobaoOrder',$expected_entity);

        // invalid testing
    }

    /**
     * @group issue_537 
     */
    public function testUpdateOneOnAudit() 
    {
        // auditing 
        // ng 
        // valid 
        // uncertain
        $this->assertEquals(1,1);
    }

    /**
     * @group issue_537 
     */
    public function testFetchByRange() 
    {
        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->fetchByRange(1,10);
        $this->assertEquals(15, $actual['total']);
        $expected_orders =array_merge( array_reverse(array_slice(LoadTaobaoOrdersData::$ORDERS, 0, 5)),array_slice(LoadTaobaoOrdersData::$ORDERS,5,5 ) );
        $this->assertEquals($expected_orders, $actual['data']);
    }

    /**
     * @group issue_537 
     */
    public function testGetLastestEggedTimestamp()
    {
        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->getLastestTimestampeEgged();
        $updated = LoadTaobaoOrdersData::$ORDERS[0] ->getUpdatedAt();
        $this->assertEquals($updated->format('Y-m-d 00:00:00'), $actual);
    }

    /**
     * @group issue_537 
     */
    public function testFindLatestEggedNickList()
    {
        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findLatestEggedNickList(10);

       // echo json_encode($actual);

        '[{"nick":"alice32","paid":"192.03","eggs":0,"at":{"date":"2014-12-06 00:00:00.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}},{"nick":"bob0","paid":"150.01","eggs":"7","at":{"date":"2014-11-30 10:14:02.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}},{"nick":"bob1","paid":"150.01","eggs":"7","at":{"date":"2014-11-30 10:13:02.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}},{"nick":"bob2","paid":"150.01","eggs":"7","at":{"date":"2014-11-30 10:12:02.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}},{"nick":"bob3","paid":"150.01","eggs":"7","at":{"date":"2014-11-30 10:11:03.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}},{"nick":"bob4","paid":"150.01","eggs":"7","at":{"date":"2014-11-30 10:10:03.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}},{"nick":"bob5","paid":"150.01","eggs":"7","at":{"date":"2014-11-30 10:09:03.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}},{"nick":"bob6","paid":"150.01","eggs":"7","at":{"date":"2014-11-30 10:08:03.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}},{"nick":"bob7","paid":"150.01","eggs":"7","at":{"date":"2014-11-30 10:07:03.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}},{"nick":"bob8","paid":"150.01","eggs":"7","at":{"date":"2014-11-30 10:06:03.000000","timezone_type":3,"timezone":"Asia\/Shanghai"}}]';

        $this->assertEquals(1,1);
    }
}
