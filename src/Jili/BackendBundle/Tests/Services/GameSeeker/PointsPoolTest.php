<?php
namespace Jili\BackendBundle\Tests\Services\GameSeeker;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\BackendBundle\DataFixtures\ORM\Services\GameSeeker\LoadPointsPoolPublishCodeData;
use Symfony\Component\Filesystem\Filesystem;

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

        if(in_array($tn, array('testPublish','testBuild','testFetch'))) {
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
     */
    public function testPublish()
    {
        $container = $this->container;
        $em = $this->em;
        $this->container->get('game_seeker.points_pool')->publish();

        $path_configs= $this->container->getParameter('game_seeker_config_path');
        $this->assertArrayHasKey('points_strategy',$path_configs);
        $root_dir = $this->container->get('kernel')->getRootDir();
        $this->assertEquals($root_dir. DIRECTORY_SEPARATOR. 'cache_data/test/game_seeker_points_strategy_conf.json',$path_configs['points_strategy']);

        // $expected  = filectime($path_configs['points_strategy']);

        // check file contents
        $expected_string = '[[1000,0],[1000,1],[500,2],[200,5],[1,500]]';
        $this->assertFileExists($path_configs['points_strategy'] );
        $this->assertStringEqualsFile( $path_configs['points_strategy'], $expected_string );
        //$this->assertEquals($expected_string,filectime($path_configs['points_strategy']));

        unlink($path_configs['points_strategy']);
    }

    /**
     * @group issue_524 
     */
    public function testGetDailyPointsPoolFile()
    {
        $path_configs= $this->container->getParameter('game_seeker_config_path');
        $file = str_replace('YYYYmmdd', date('Ymd'), $path_configs['points_pool']);
        $this->assertEquals($file, $this->container->get('game_seeker.points_pool')->getDailyPointsPoolFile());
    }

    /**
     * @group issue_524 
     */
    public function testBuild()
    {
        // write fixture for testing.
        // file cached file exists, query is not execute.
        $fixture_string = '[[1000,0],[1000,1],[500,2],[200,5],[1,500]]';
        $path_configs= $this->container->getParameter('game_seeker_config_path');

        $root_dir = $this->container->get('kernel')->getRootDir();
        $fixture_file =  $path_configs['points_strategy'];
        $fs = new Filesystem();
        $fixture_dir = dirname($fixture_file);
        if(! $fs->exists($fixture_dir)){
            $fs->mkdir( $fixture_dir);
        }
        file_put_contents( $fixture_file,$fixture_string);

        $expected_ctime  =  filectime( $fixture_file);
// 
        // do request
        $this->container->get('game_seeker.points_pool')->build();
        $file = str_replace('YYYYmmdd', date('Ymd'), $path_configs['points_pool']);
        $this->assertFileExists($file );

        $c = file_get_contents($file);
        $pool_actual = json_decode($c, true);

        // check the size , elements count
        // $fixture_string = '[[1000,0],[1000,1],[500,2],[200,5],[1,500]]';
        // 0 pts, count = 1000;
        $this->assertCount(2701,   $pool_actual);
        $this->assertCount(1000,  array_filter( $pool_actual, function($v) {
            return $v === 0;
        }  ));
        $this->assertCount(1000,  array_filter( $pool_actual, function($v) {
            return $v === 1;
        }  ));
        $this->assertCount(500,  array_filter( $pool_actual, function($v) {
            return $v === 2;
        }  ));
        $this->assertCount(200,  array_filter( $pool_actual, function($v) {
            return $v === 5;
        }  ));
        $this->assertCount(1,  array_filter( $pool_actual, function($v) {
            return $v === 500;
        }  ));

        $this->assertEquals($expected_ctime  ,  filectime( $fixture_file));
        unlink($fixture_file);
        unlink($file);
    }

    /**
     * @group issue_524 
     */
    public function testFetch()
    {
        $path_configs= $this->container->getParameter('game_seeker_config_path');
        $file = str_replace('YYYYmmdd', date('Ymd'), $path_configs['points_pool']);
        // let fetch geneate auto.
        @unlink($file); 

        $i = 2700;
       for($i ; $i>=0; $i--) {
            $fetched = $this->container->get('game_seeker.points_pool')->fetch();
            // after.
            $this->assertFileExists($file );
            // count reduced 1 
            $c = file_get_contents($file);
            $pool_actual = json_decode($c, true);
            // check the size , elements count
            $this->assertCount($i, $pool_actual);
            unset($c);
            unset($fetched);
            unset($pool_actual);
        }

        // fetch out of range.
        $fetched = $this->container->get('game_seeker.points_pool')->fetch();
        $this->assertNull($fetched);

        unlink($file);
    }

    /**
     * @group issue_524 
     */
    public function testFetchChestCount()
    {
        $this->assertEquals(5, \Jili\BackendBundle\Services\GameSeeker\PointsPool::CHEST_COUNT);
        $path_configs= $this->container->getParameter('game_seeker_config_path');
        $this->assertArrayHasKey('chest', $path_configs);
        $file =  $path_configs['chest'];

        $root_dir = $this->container->get('kernel')->getRootDir();
        $this->assertEquals($root_dir. DIRECTORY_SEPARATOR. 'cache_data/test/game_seeker_config_chest.txt',$path_configs['chest']);

        $this->container->get('game_seeker.points_pool')->fetchChestCount();
        $this->assertFileExists($file);
        $this->assertStringEqualsFile($file, 5);
        unlink($file);

    }

    /**
     * @group issue_524 
     */
    public function testUpdateChestCount(  ) 
    {

        $path_configs= $this->container->getParameter('game_seeker_config_path');
        $this->assertArrayHasKey('chest', $path_configs);
        $file =  $path_configs['chest'];
        @unlink($file);

        $this->container->get('game_seeker.points_pool')->updateChestCount(17);
        $this->assertFileExists($file);
        $this->assertStringEqualsFile($file, 17);
        unlink($file);

    }
}
