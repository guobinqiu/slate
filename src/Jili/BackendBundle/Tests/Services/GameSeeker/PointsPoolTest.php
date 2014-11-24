<?php
namespace Jili\BackendBundle\Tests\Services\GameSeeker;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\BackendBundle\DataFixtures\ORM\Services\GameSeeker\LoadPointsPoolPublishCodeData;

class PointsPoolTest extends KernelTestCase
{
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $container =  static::$kernel->getContainer();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $loader = new Loader();
        $fixture= new LoadPointsPoolPublishCodeData();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());
        $this->em = $em;
        $this->container = $container;
    }


    /**
     * @group issue_524
     * @group debug 
     */
    public  function testPublish()
    {
        $container = $this->container;
        $em = $this->em;
        $this->container->get('game_seeker.points_pool')->publish();

        $path_configs= $this->container->getParameter('game_seeker_config_path');
        $this->assertArrayHasKey('points_strategy',$path_configs);
        echo $path_configs['points_strategy'],PHP_EOL;
        
        $root_dir = $this->container->get('kernel')->getRootDir();

        $this->assertEquals($root_dir. DIRECTORY_SEPARATOR. 'cache_data/test/game_seeker_points_strategy_conf.json',$path_configs['points_strategy']);

        //  check file contents
        //  [[1000,0],[1000,1],[500,2],[200,5],[1,500]]
        $actual_string  = '[[1000,0],[0,1],[1000,2],[1,3],[500,4],[2,5],[200,6],[5,7],[1,8],[500,9]]';
        $this->assertFileExists($path_configs['points_strategy'] );
        $this->assertStringEqualsFile($path_configs['points_strategy'] , $actual_string);

    }
}
