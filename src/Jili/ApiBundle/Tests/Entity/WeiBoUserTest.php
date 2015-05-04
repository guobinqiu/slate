<?php
namespace Jili\ApiBundle\Tests\Entity;
use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
class WeiBoUserTest extends KernelTestCase
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
        $this->container = $container;
        $this->em = $em;
    }
    public function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }
    /**
    * @group issue636
    */
    public function testWeiBouser_insert()
    {
        $param = array('user_id'=> 1, 'open_id'=> '973F697E97A60289C8C455B1D65FF5F0' );
        $r = $this->em->getRepository('JiliApiBundle:WeiBoUser')->weibo_user_insert($param);
        $this->assertEquals(1,$r->getUserId() );
        $this->assertEquals('973F697E97A60289C8C455B1D65FF5F0', $r->getOpenId() );
    }
}
