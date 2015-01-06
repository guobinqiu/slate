<?php
namespace Jili\BackendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\GameEggsBreakerTaobaoOrder\LoadLogsData;

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

        $tn = $this->getName();
        if (in_array($tn, array('testGetLastestTimestampBroken','testFindLatestBrokenNickList'))) {
            // load fixtures
            $loader = new Loader();
            $loader->addFixture(new LoadLogsData());
            $executor->execute($loader->getFixtures());
            $this->has_fixture = true;
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
    
    /**
     * @group issue_537
     */
    public function testGetLastestTimestampBroken()
    {
        $t = $this->em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')
            ->getLastestTimestampBroken();
        $log = LoadLogsData::$LOGS[1];

        $this->assertEquals($log->getCreatedAt()->format('Y-m-d H:i:s') ,$t);
    }

    /**
     * @group issue_537
     */
    public function testFindLatestBrokenNickList()
    {

        $stat  = $this->em->getRepository('JiliFrontendBundle:GameEggsBrokenLog')
            ->findLatestBrokenNickList(10);
        $this->assertCount(10,$stat);

        $fixtures = array(
            array('nick'=> LoadLogsData::$USERS[0]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[1]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[1]->getCreatedAt()->format("Y-m-d H:i:s")),
            array('nick'=> LoadLogsData::$USERS[1]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[2]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[2]->getCreatedAt()->format("Y-m-d H:i:s")),
            array('nick'=> LoadLogsData::$USERS[11]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[12]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[12]->getCreatedAt()->format("Y-m-d H:i:s")),
            array('nick'=> LoadLogsData::$USERS[2]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[3]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[3]->getCreatedAt()->format("Y-m-d H:i:s")),
            array('nick'=> LoadLogsData::$USERS[12]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[13]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[13]->getCreatedAt()->format("Y-m-d H:i:s")),
            array('nick'=> LoadLogsData::$USERS[4]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[5]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[5]->getCreatedAt()->format("Y-m-d H:i:s")),
            array('nick'=> LoadLogsData::$USERS[13]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[14]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[14]->getCreatedAt()->format("Y-m-d H:i:s")),
            array('nick'=> LoadLogsData::$USERS[5]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[6]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[6]->getCreatedAt()->format("Y-m-d H:i:s")),
            array('nick'=> LoadLogsData::$USERS[14]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[15]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[15]->getCreatedAt()->format("Y-m-d H:i:s")),
            array('nick'=> LoadLogsData::$USERS[6]->getNick(),
            'pointsAcquried'=> LoadLogsData::$LOGS[7]->getPointsAcquried(),
            'at'=> LoadLogsData::$LOGS[7]->getCreatedAt()->format("Y-m-d H:i:s")),
        );
        $this->assertEquals($fixtures, $stat);
    }

}
