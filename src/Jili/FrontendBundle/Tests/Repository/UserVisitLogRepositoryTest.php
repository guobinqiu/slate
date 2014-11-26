<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\UserVisitLog\LoadIsGameSeekerDoneData;
use Jili\FrontendBundle\Entity\UserVisitLog;

class UserVisitLogRepositoryTest extends KernelTestCase 
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $has_fixture = false;

    /**
     * {@inheritDoc}
     */
    public function setUp() 
    {
        static::$kernel = static :: createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        $tn = $this->getName();
        if( $tn === 'testIsGameSeekerDoneDaily') {
            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();

            // load fixtures
            $loader = new Loader();
            $loader->addFixture(new LoadIsGameSeekerDoneData());
            $executor->execute($loader->getFixtures());
            $this->has_fixture = true;
        }
        $this->em = $em;
    }


    /**
     * {@inheritDoc}
     */
    protected function tearDown() 
    {
        parent :: tearDown();
        if($this->has_fixture ) {
            $this->em->close();
        }
    }

    /**
     * @group issue_524
     */
    function testLogGameSeeker() 
    {
        // insert data
        $entity = new UserVisitLog();
        $this->assertEquals(3, UserVisitLog::TARGET_FLAG_GAME_SEEKER); 
        $em =$this->em;
        $acutal = $em->getRepository('JiliFrontendBundle:UserVisitLog')->logGameSeeker(array('userId'=>1));
        $expected = $em->getRepository('JiliFrontendBundle:UserVisitLog')->findOneByUserId(1 );
        $this->assertEquals(serialize($expected), serialize($acutal));
    }

    /**
     * @group issue_524
     * @group debug 
     */
    function testIsGameSeekerDoneDaily( )
    {
        $em = $this->em;

        $result  = $em->getRepository('JiliFrontendBundle:UserVisitLog')->isGameSeekerDoneDaily(1 );
        $this->assertEquals(1, $result);
        $result  = $em->getRepository('JiliFrontendBundle:UserVisitLog')->isGameSeekerDoneDaily(11 );
        // select good one!
        $this->assertEquals(1, $result);
        // select bad one!
        $result  = $em->getRepository('JiliFrontendBundle:UserVisitLog')->isGameSeekerDoneDaily(19 );
        $this->assertEquals(0, $result);
    }

}
