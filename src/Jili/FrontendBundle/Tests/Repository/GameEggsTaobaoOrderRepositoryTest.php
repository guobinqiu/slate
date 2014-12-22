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
        if(in_array($tn , array('testGetLastestTimestampEgged','testUpdateOne','testFetchByRange','testFindLatestEggedNickList','testFindOneForAudit'))) {
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
    public function testFetchByRange() 
    {
        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->fetchByRange(1,10);
        $this->assertEquals(15, $actual['total']);

         $expected_orders  =array_merge( array_reverse(array_slice(LoadTaobaoOrdersData::$ORDERS, 0, 5)),array_slice(LoadTaobaoOrdersData::$ORDERS,5,5 ) );

        $expected = array(
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[0]),
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[1]),
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[2]),
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[3]),
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[4]),
            array('email'=> 'bob0@gmail.com',0=> $expected_orders[5]),
            array('email'=> 'bob1@gmail.com',0=> $expected_orders[6]),
            array('email'=> 'bob2@gmail.com',0=> $expected_orders[7]),
            array('email'=> 'bob3@gmail.com',0=> $expected_orders[8]),
            array('email'=> 'bob4@gmail.com',0=> $expected_orders[9]),
        );
        $this->assertEquals($expected, $actual['data']);

        // p=1 size = 7
        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->fetchByRange(1,7);
        $this->assertEquals(15, $actual['total']);

         $expected_orders  =array_merge( array_reverse(array_slice(LoadTaobaoOrdersData::$ORDERS, 0, 5)),array_slice(LoadTaobaoOrdersData::$ORDERS,5,5 ) );

        $expected = array(
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[0]),
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[1]),
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[2]),
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[3]),
            array('email'=> 'alice32@gmail.com',0=> $expected_orders[4]),
            array('email'=> 'bob0@gmail.com',0=> $expected_orders[5]),
            array('email'=> 'bob1@gmail.com',0=> $expected_orders[6]),
        );
        $this->assertEquals($expected, $actual['data']);
        // p=3, size=3
        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->fetchByRange(3,3);
        $this->assertEquals(15, $actual['total']);

         $expected_orders  =array_merge( array_reverse(array_slice(LoadTaobaoOrdersData::$ORDERS, 0, 5)),array_slice(LoadTaobaoOrdersData::$ORDERS,5,5 ) );

        $expected = array(
            array('email'=> 'bob1@gmail.com',0=> $expected_orders[6]),
            array('email'=> 'bob2@gmail.com',0=> $expected_orders[7]),
            array('email'=> 'bob3@gmail.com',0=> $expected_orders[8]),
        );
        $this->assertEquals($expected, $actual['data']);

        // add filters  [ )
        $updated = new \Datetime();
        $updated->sub(new \DateInterval('P12D'));
        $updated->setTime(0,0);

        $finish = new \Datetime();
        $finish->sub(new \DateInterval('P10D'));

        $filters = array(
            'begin' =>  $updated,
            'finish' => $finish
        );

        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->fetchByRange(2,3, $filters);
        $this->assertEquals(10, $actual['total']);

        $expected = array(
            array('email'=> 'bob3@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[8]),
            array('email'=> 'bob4@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[9]),
            array('email'=> 'bob5@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[10]),
        );

        $this->assertEquals($expected, $actual['data']);

        // ( ]
        $begin= new \Datetime();
        $begin->sub(new \DateInterval('P14D'));
        $begin->setTime(0,0);

        $updated = new \Datetime();
        $updated->sub(new \DateInterval('P12D'));
        $updated->setTime(0,0);

        $filters = array(
            'begin' =>  $begin,
            'finish' => $updated 
        );

        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->fetchByRange(2,3, $filters);
        $this->assertEquals(0, $actual['total']);

        // [ ]

        $updated = new \Datetime();
        $updated->sub(new \DateInterval('P12D'));
        $updated->setTime(0,0);

        $finish  = new \Datetime();
        $finish->setTime(0,0);

        $filters = array(
            'begin' =>  $updated,
            'finish' => $finish 
        );
        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->fetchByRange(1,7,$filters);
        $this->assertEquals(10, $actual['total']);

         $expected_orders  =array_merge( array_reverse(array_slice(LoadTaobaoOrdersData::$ORDERS, 0, 5)),array_slice(LoadTaobaoOrdersData::$ORDERS,5,5 ) );

        $expected = array(
            array('email'=> 'bob0@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[5]),
            array('email'=> 'bob1@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[6]),
            array('email'=> 'bob2@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[7]),
            array('email'=> 'bob3@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[8]),
            array('email'=> 'bob4@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[9]),
            array('email'=> 'bob5@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[10]),
            array('email'=> 'bob6@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[11]),
        );

        $this->assertEquals($expected, $actual['data']);

        //[ , 1+ ]
        $updated = new \Datetime();
        $updated->sub(new \DateInterval('P12D'));
        $updated->setTime(0,0);

        $finish  = new \Datetime();
        $finish->add(new \DateInterval('P1D'));
        $finish->setTime(0,0);

        $filters = array(
            'begin' =>  $updated,
            'finish' => $finish 
        );
        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->fetchByRange(2,5,$filters);
        $this->assertEquals(15, $actual['total']);

         $expected_orders  =array_merge( array_reverse(array_slice(LoadTaobaoOrdersData::$ORDERS, 0, 5)),array_slice(LoadTaobaoOrdersData::$ORDERS,5,5 ) );

        $expected = array(
            array('email'=> 'bob0@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[5]),
            array('email'=> 'bob1@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[6]),
            array('email'=> 'bob2@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[7]),
            array('email'=> 'bob3@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[8]),
            array('email'=> 'bob4@gmail.com',0=> LoadTaobaoOrdersData::$ORDERS[9]),
        );

        $this->assertEquals($expected, $actual['data']);
    }


    /**
     * @group issue_537 
     */
    public function testGetLastestTimestampEgged()
    {
        $actual = $this->em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->getLastestTimestampEgged();
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

        $expected = array(
            0 => 
            array (
                'nick' => 'alice32',
                'paid' => '102.01',
                'countOfEggs' => 0,
                'at' =>  LoadTaobaoOrdersData::$ORDERS[0]->getUpdatedAt()
            ),
            1 => 
            array (
                'nick' => 'bob0',
                'paid' => '150.01',
                'countOfEggs' => '4',
                'at' => LoadTaobaoOrdersData::$ORDERS[5]->getUpdatedAt()
            ),
            2 => 
            array (
                'nick' => 'bob1',
                'paid' => '150.01',
                'countOfEggs' => '8',
                'at' => LoadTaobaoOrdersData::$ORDERS[6]->getUpdatedAt()
            ),
            3 => 
            array (
                'nick' => 'bob2',
                'paid' => '150.01',
                'countOfEggs' => '12',
                'at' => LoadTaobaoOrdersData::$ORDERS[7]->getUpdatedAt()
            ),
            4 => 
            array (
                'nick' => 'bob3',
                'paid' => '150.01',
                'countOfEggs' => '16',
                'at' => LoadTaobaoOrdersData::$ORDERS[8]->getUpdatedAt()
            ),
            5 => 
            array (
                'nick' => 'bob4',
                'paid' => '150.01',
                'countOfEggs' => '20',
                'at' => LoadTaobaoOrdersData::$ORDERS[9]->getUpdatedAt()
            ),
            6 => 
            array (
                'nick' => 'bob5',
                'paid' => '150.01',
                'countOfEggs' => '24',
                'at' => LoadTaobaoOrdersData::$ORDERS[10]->getUpdatedAt()
            ),
            7 => 
            array (
                'nick' => 'bob6',
                'paid' => '150.01',
                'countOfEggs' => '28',
                'at' => LoadTaobaoOrdersData::$ORDERS[11]->getUpdatedAt()
            ),
            8 => 
            array (
                'nick' => 'bob7',
                'paid' => '150.01',
                'countOfEggs' => '32',
                'at' => LoadTaobaoOrdersData::$ORDERS[12]->getUpdatedAt()
            ),
            9 => 
            array (
                'nick' => 'bob8',
                'paid' => '150.01',
                'countOfEggs' => '36',
                'at' => LoadTaobaoOrdersData::$ORDERS[13]->getUpdatedAt()
            ),
        );

        $this->assertEquals($expected, $actual);
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
    public function testFindOneForAudit()
    {
        $em = $this->em;
        // id not exists
        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneForAudit(9999);
        $this->assertNull($actual);

        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneForAudit(LoadTaobaoOrdersData::$ORDERS[5] ->getId() );
        $this->assertNull($actual);

        // id already completed exists
        $actual = $em->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneForAudit(LoadTaobaoOrdersData::$ORDERS[2] ->getId() );
        $this->assertNotNull($actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerTaobaoOrder', $actual);

    }
}
