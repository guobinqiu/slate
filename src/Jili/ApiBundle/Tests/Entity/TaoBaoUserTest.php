<?php
namespace Jili\ApiBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

// use Jili\ApiBundle\DataFixtures\ORM\Entity\LoadAdvertisermentCodeData;


class TaoBaoUserTest extends KernelTestCase
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
        $container = static :: $kernel->getContainer();

            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();

  //              $fixture = new LoadAdvertisermentCodeData(); 
  //          $loader = new Loader();
  //          $loader->addFixture($fixture);
  //          $executor->execute($loader->getFixtures());

        $this->container = $container;
        $this->em = $em;
    }

    public function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_536
     */
    public function testTaoBaouser_insert()
    {
        $param = array('user_id'=> 1, 'open_id'=> '973F697E97A60289C8C455B1D65FF5F0' );

        $r = $this->em->getRepository('JiliApiBundle:TaoBaoUser')->taobao_user_insert($param); 

        $this->assertEquals(1,$r->getUserId() );
        $this->assertEquals('973F697E97A60289C8C455B1D65FF5F0', $r->getOpenId() );

    }
}

