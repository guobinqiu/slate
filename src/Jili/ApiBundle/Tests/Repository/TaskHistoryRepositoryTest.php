<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader; 
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;

use Jili\ApiBundle\DataFixtures\ORM\Repository\DuomaiOrder\LoadInitData;

class TaskHistoryRepositoryTest extends KernelTestCase  
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
        if (in_array($tn, array('testUpdate'))) {
            $fixture = new LoadInitData();
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }

        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @group issue_680
     */
    public function testUpdate()
    {
        $em = $this->em;
        $return =$em->getRepository('JiliApiBundle:TaskHistory05')
            ->update( array(
                'userId'=> 105, 
                'orderId'=> 1,
                'categoryType'=> \Jili\ApiBundle\Entity\AdCategory::ID_DUOMAI ,
                'taskType' => \Jili\ApiBundle\Entity\TaskHistory00::TASK_TYPE_DUOMAI,
                'point'=> intval (5.4* 70),
                'rewardPercent' =>70, 
                'status' => 2,
                'statusPrevious'=> 1 
            ));


        $this->assertNotNull( $return);

        $this->assertEquals(1, $return);
        $duomai_task_stm =   $em->getConnection()->prepare('select * from task_history05');
        $duomai_task_stm->execute();
        $duomai_task_records =$duomai_task_stm->fetchAll();

        $this->assertCount(1, $duomai_task_records);
        $this->assertEquals('2', $duomai_task_records[0]['status']);
        $this->assertEquals( 378,  $duomai_task_records[0]['point']);
    }

}
