<?php
namespace  Jili\ApiBundle\Tests\Services\Bangwoya;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
use Jili\ApiBundle\Entity\User;

class BangwoyaRequestProcessorTest extends KernelTestCase {

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

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $fixture = new LoadUserData();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->container = static :: $kernel->getContainer();
        $this->em = $em;
    }

    /**
     * @group issue_578
     * @group debug
     */
    public function testProcess() {
        $em = $this->em;
        $container = $this->container;

        $config = $container->getParameter('bangwoya_com');

        $users = LoadUserData :: $USERS;
        $user = $users[0];
        $partnerid = $user->getId();
        $tid = '123456789';
        $vmoney = 100;

        $user = $em->getRepository('JiliApiBundle:User')->find($partnerid);
        $oldPoint = $user->getPoints();

        // get service
        $bangwoya_service = $container->get('bangwoya_request.processor');
        $bangwoya_service->process($tid, $partnerid, $vmoney);

        //bangwoya
        $order = $em->getRepository('JiliApiBundle:BangwoyaOrder')->findOneByTid($tid);
        $this->assertEquals(1, count($order));

        //task_history
        $taskHistory = $em->getRepository('JiliApiBundle:TaskHistory0' . ($partnerid % 10))->findOneByUserId($partnerid);
        $this->assertEquals($partnerid, $taskHistory->getUserId());
        $this->assertEquals($config['category_type'], $taskHistory->getCategoryType());
        $this->assertEquals($config['task_type'], $taskHistory->getTaskType());
        $this->assertEquals($config['name'], $taskHistory->getTaskName());

        //point_history
        $pointHistory = $em->getRepository('JiliApiBundle:PointHistory0' . ($partnerid % 10))->findOneByUserId($partnerid);
        $this->assertEquals($config['category_type'], $pointHistory->getReason());
        $this->assertEquals($vmoney, $pointHistory->getPointChangeNum());

        //update user.point更新user表总分数
        $user = $em->getRepository('JiliApiBundle:User')->find($partnerid);
        $newPoint = $user->getPoints();
        $this->assertEquals($vmoney, $newPoint - $oldPoint);

        // 测试 rollback
        $bangwoya_service->process($tid, $partnerid, $vmoney);
        $user = $em->getRepository('JiliApiBundle:User')->find($partnerid);
        $rollbackPoint = $user->getPoints();
        $this->assertEquals(0, $rollbackPoint - $newPoint);
    }
}