<?php
namespace  Jili\ApiBundle\Tests\Services\Points;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;


class ManagerTest extends KernelTestCase 
{

    /**
     */
    public function testUpdatePoints() 
    {

        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $container = static::$kernel->getContainer();

        $service = $container->get('points_manager');

        $this->assertInstanceOf( 'Jili\ApiBundle\Services\Points\Manager', $service, 'class name') ;

        $em = $container->get('doctrine')->getManager();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

// run test
        $service->updatePoints(1,7,93,9, '同意参加Fulcrum调查' );
// check result

        $user_stm =   $em->getConnection()->prepare('select * from user where id =  1');
        $user_stm->execute();
        $user_updated =$user_stm->fetchAll();

        $this->assertNotEmpty($user_updated,'1 test user');
        $this->assertCount(1, $user_updated,'1 test user');
        $this->assertEquals(107, $user_updated[0]['points'], '100 +7');

        $points_stm =   $em->getConnection()->prepare('select * from point_history01 where user_id =  1');
        $points_stm->execute();
        $points_history =$points_stm->fetchAll();

        $this->assertNotEmpty($points_history,'1 point history record');
        $this->assertCount(1, $points_history,'1 point history record');
        $this->assertEquals(7, $points_history[0]['point_change_num'],'7 points');
        $this->assertEquals(93, $points_history[0]['reason'],'ad_cateogry 93');

        $task_stm =   $em->getConnection()->prepare('select * from task_history01 where user_id =  1');

        $task_stm->execute();
        $task_history=$task_stm->fetchAll();

        $this->assertNotEmpty($task_history,'1 point history record');
        $this->assertCount(1, $task_history,'1 point history record');
        $this->assertEquals(9, $task_history[0]['task_type'],'suvey task9');
        $this->assertEquals(93, $task_history[0]['category_type'],'ad_cateogry 93');
        $this->assertEquals('同意参加Fulcrum调查', $task_history[0]['task_name'],'task name');
    }
}

