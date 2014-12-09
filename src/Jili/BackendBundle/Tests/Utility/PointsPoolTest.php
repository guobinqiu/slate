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
        $pointsPool = new PointsPool( $config['common'] ['points_pool'], $config['common']['points_strategy']);

        $this->assertEquals(1,1);
    }

}
