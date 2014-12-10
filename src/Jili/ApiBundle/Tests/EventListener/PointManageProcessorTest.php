<?php
namespace Jili\ApiBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class PointManageProcessorTest extends KernelTestCase {

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

        // purge tables;
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
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_560
     */
    public function testProcess() {
        $em = $this->em;
        $container = $this->container;
        $user = LoadUserData :: $USERS[0];

        $user1 = $em->getRepository('JiliApiBundle:User')->find($user->getId());
        $points1 = $user1->getPoints();

        $directory = $container->get('kernel')->getBundle('JiliApiBundle')->getPath();
        $directory .= '/DataFixtures/ORM/Services';

        $content = "user_id,email,point,task_name,category_type,task_type\r\n";
        $content .= $user->getId() . ",,100,测试手动发送积分,90,4";

        $path = $directory . '/PointManageProcessorTest_commit.csv';
        $log_path = $directory . '/PointManageProcessorTest_commit_log.csv';
        file_put_contents($path, $content);

        $message = $this->container->get('point_manage.processor')->process($path, $log_path);
        $this->assertEquals('导入成功', $message['success']);

        $user2 = $em->getRepository('JiliApiBundle:User')->find($user->getId());
        $points2 = $user2->getPoints();
        $this->assertEquals(100, ($points2 - $points1));

        $pointHistory = $em->getRepository('JiliApiBundle:PointHistory0' . ($user->getId() % 10))->findOneByUserId($user->getId());
        $this->assertEquals(1, count($pointHistory));

        $taskHistory = $em->getRepository('JiliApiBundle:TaskHistory0' . ($user->getId() % 10))->findOneByUserId($user->getId());
        $this->assertEquals(1, count($taskHistory));

        // TODO: rollback
        //$message = $this->container->get('point_manage.processor')->process($path, $log_path);
        //$this->assertEquals('rollback.导入失败，请查明原因再操作', $message['code'][0]);

        unlink($path);
        unlink($log_path);
    }

}