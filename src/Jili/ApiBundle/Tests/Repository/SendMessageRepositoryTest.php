<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class SendMessageRepositoryTest extends KernelTestCase
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
        $container = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

      //  $loader = new Loader();
      //  $fixture = new LoadIssetInsertData();
      //  $loader->addFixture($fixture);
        //$executor->execute($loader->getFixtures());
        
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group debug
     * @group issue_592 
     */
    public function testInsertSendMs()
    {
        $em = $this->em;

        for( $i = 10 ; $i >=1  ; $i--) {
            $params  =  array(
                'userid' => $i,
                'title' => '订单审核结果',
                'content' =>'订单号xxxx已经通过，可以得到?枚金蛋。可去[砸蛋]' 
            );

            $em->getRepository('JiliApiBundle:SendMessage0'.(  $params['userid'] % 10 ))->insertSendMs($params);

            $expected = $em->getRepository('JiliApiBundle:SendMessage0'. (  $params['userid'] % 10 ))
                ->findBy( array('sendTo' => $params['userid']));

            $this->assertNotNull($expected);
            $this->assertCount(1, $expected);

            $this->assertInstanceOf('\\Jili\\ApiBundle\\Entity\\SendMessage0'. (  $params['userid'] % 10 ), 
            $expected[0]);
        } 
    }
}

