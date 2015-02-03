<?php
namespace Jili\BackendBundle\Tests\Utility;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Jili\BackendBundle\Utility\PointsPool;

class PointsPoolTest extends KernelTestCase
{
    /**
     * @group issue_537
     */
    public function testBuild() 
    {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        //$em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();
        $config = $container->getParameter('game_eggs_breaker');
        $path = $container->getParameter('cache_data_path');

        $pointsPool = new PointsPool( $config['common'] ['points_pool'], $config['common']['points_strategy']);
        $file = $pointsPool->getPointsPoolFile();

        $expeced = $path.'/game_eggs_breaker/common/points_pool_'.date('Ymd').'.json' ; 
        $this->assertEquals($file, $expeced);

        $this->assertEquals(1,1);
        $pointsPool = new PointsPool( $config['common'] ['points_pool'], $config['common']['points_strategy'], false);
        $file = $pointsPool->getPointsPoolFile();

        $expeced = $path.'/game_eggs_breaker/common/points_pool_.json' ; 
        $this->assertEquals($expeced, $file  );

        $pointsPool = new PointsPool( $config['consolation'] ['points_pool'], $config['consolation']['points_strategy']);
        $file = $pointsPool->getPointsPoolFile();

        $expeced = $path.'/game_eggs_breaker/consolation/points_pool_'.date('Ymd').'.json' ; 
        $this->assertEquals($file, $expeced);

        $this->assertEquals(1,1);
        $pointsPool = new PointsPool( $config['consolation'] ['points_pool'], $config['consolation']['points_strategy'], false);
        $file = $pointsPool->getPointsPoolFile();

        $expeced = $path.'/game_eggs_breaker/consolation/points_pool_.json' ; 
        $this->assertEquals($expeced, $file  );
    }

}
