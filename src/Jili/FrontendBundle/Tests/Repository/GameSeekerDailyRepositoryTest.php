<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\FrontendBundle\DataFixtures\ORM\Repository\GameSeekerDaily\LoadGetInfoByUserData;

class GameSeekerDailyRepositoryTest  extends KernelTestCase 
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
        $container  = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $fixture = new LoadGetInfoByUserData();

        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->purge();
        $executor->execute($loader->getFixtures());

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
     * @group issue_524 
     * @group debug 
     */
    public function testGetInfoByUser() {
        $em = $this->em;
        $this->assertEquals(1,1);
        $today = new \DateTime();//date('Y-m-d');
        $instance = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=>1, 'createdDay'=>$today ));
        $this->assertNull($instance);
        $instance = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->getInfoByUser(1);
echo serialize($instance);
 //       $this->assertNotNull($instance);

  //      $instance_after = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=>1, 'createdDay'=>date('Y-m-d H:i:s')));

        //$this->assertNotNull($instance);
        //$instance = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->getInfoByUser(10);

    }
}
