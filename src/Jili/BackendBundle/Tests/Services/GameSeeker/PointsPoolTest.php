<?php
namespace Jili\BackendBundle\Tests\Services\GameSeeker;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\BackendBundle\DataFixtures\ORM\Services\GameSeeker\LoadPointsPoolPublishCodeData;

class PointsPoolTest extends KernelTestCase
{
    private $has_fixture = false;
    

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $container =  static::$kernel->getContainer();
        
        $tn = $this->getName();
        if( 'testPublish'=== $tn) {
            $this->has_fixture  = true;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();

            $loader = new Loader();
            $fixture= new LoadPointsPoolPublishCodeData();
            $loader->addFixture($fixture);

            $executor->execute($loader->getFixtures());
        }

        $this->em = $em;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        if($this->has_fixture) {
            $this->em->close();
        }
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
        $expected_string = '[[1000,0],[1000,1],[500,2],[200,5],[1,500]]';
        $this->assertFileExists($path_configs['points_strategy'] );
        $this->assertStringEqualsFile( $path_configs['points_strategy'], $expected_string );
    }
}
