<?php
namespace  Jili\BackendBundle\Tests\Services\GameEggsBreaker;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
class GameEggsBreakerTest extends KernelTestCase
{

    /**
     * @group issue_527
     */
    public function testPublishPointsStrategy() 
    {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        //$em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();
/// get service 
        $game_eggs_breakers  = $container->get('december_activity.game_eggs_breaker');

        $configs = $container->getParameter('game_eggs_breaker');
        $cache_dir =$container->getParameter('cache_data_path');
        $file_expected = $cache_dir.'/game_eggs_breaker/common/points_strategy_conf.json';

        $this->assertEquals($file_expected,$configs['common']['points_strategy']);

        @unlink($file_expected);

        $fixture = "1000:1\n7000:0\n200:10";
        $game_eggs_breakers->publishPointsStrategy($fixture, 'common');
        // check file exists
        $this->assertFileExists($file_expected, 'common points strategy config file should be created' );

        $string_expected = json_encode(array( array(
            1000,1), array( 7000,0), array(200,10)));
        $this->assertStringEqualsFile($file_expected, $string_expected);
        @unlink($file_expected);
    }

    /**
     * @group issue_527
     */
    public function testFetchRandomPoints() 
    {

        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        //$em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        // prepare config cache
        $configs = $container->getParameter('game_eggs_breaker');
        $cache_dir =$container->getParameter('cache_data_path');
        $file_expected = $cache_dir.'/game_eggs_breaker/common/points_strategy_conf.json';

        if( file_exists($file_expected)) {
            unlink($file_expected);
        }

        $dir = dirname($file_expected);
        if( ! file_exists($dir)) {
            mkdir(  $dir , 0700 , true) ;
        }

        // I
        file_put_contents( $file_expected, json_encode(array(array(1,7))));
        
        $file_pool = str_replace('YYYYmmdd', date('Ymd'), $configs['common']['points_pool']);
        if(file_exists($file_pool)){
            unlink($file_pool);
        }

        // get service 
        $game_eggs_breakers  = $container->get('december_activity.game_eggs_breaker');
        // test fetch 
        $actual_points = $game_eggs_breakers->fetchRandomPoints('common');
        // check points pool built
        $this->assertEquals(7, $actual_points);
        $this->assertStringEqualsFile( $file_expected,'[[1,7]]');
        $this->assertFileExists( $file_pool);
        $this->assertStringEqualsFile( $file_pool,'[]');

        // II. 
        if(file_exists($file_pool)){
            unlink($file_pool);
        }

        if( file_exists($file_expected)) {
            unlink($file_expected);
        }

        file_put_contents( $file_expected, json_encode(array(array(1,0))));
        $actual_points = $game_eggs_breakers->fetchRandomPoints('common');
        // check points pool built
        $this->assertEquals(0, $actual_points);
        $this->assertStringEqualsFile( $file_expected,'[[1,0]]');
        $this->assertFileExists( $file_pool);
        $this->assertStringEqualsFile( $file_pool,'[]');
        
        $actual_points = $game_eggs_breakers->fetchRandomPoints('common');
        // check points pool built
        $this->assertNull( $actual_points);
        $this->assertFileExists( $file_pool);
        $this->assertStringEqualsFile( $file_pool,'[]');

        // III
        if(file_exists($file_pool)){
            unlink($file_pool);
        }

        if( file_exists($file_expected)) {
            unlink($file_expected);
        }

        file_put_contents( $file_expected, json_encode(array(array(3,0))));
        $actual_points = $game_eggs_breakers->fetchRandomPoints('common');
        // check points pool built
        $this->assertSame(0, $actual_points);
        $this->assertStringEqualsFile( $file_expected,'[[3,0]]');
        $this->assertFileExists( $file_pool);
        $this->assertStringEqualsFile( $file_pool,'[0,0]');
        
        $actual_points = $game_eggs_breakers->fetchRandomPoints('common');
        $this->assertSame(0, $actual_points);
        $this->assertFileExists( $file_pool);
        $this->assertStringEqualsFile( $file_pool,'[0]');

        $actual_points = $game_eggs_breakers->fetchRandomPoints('common');
        $this->assertSame(0, $actual_points);
        $this->assertFileExists( $file_pool);
        $this->assertStringEqualsFile( $file_pool,'[]');

        $actual_points = $game_eggs_breakers->fetchRandomPoints('common');
        $this->assertNull( $actual_points);
         $this->assertFileExists( $file_pool);
        $this->assertStringEqualsFile( $file_pool,'[]');

        $actual_points = $game_eggs_breakers->fetchRandomPoints('common');
        $this->assertNull( $actual_points);
        $this->assertFileExists( $file_pool);
        $this->assertStringEqualsFile( $file_pool,'[]');


        if(file_exists($file_pool)){
            unlink($file_pool);
        }

        if( file_exists($file_expected)) {
            unlink($file_expected);
        }

        // IV
        if(file_exists($file_pool)){
            unlink($file_pool);
        }
        if( file_exists($file_expected)) {
            unlink($file_expected);
        }

        file_put_contents( $file_expected, json_encode(array(array(3,5), array(1,11) ,array(2,7))));
        $actual_points = 1;
        for($i = 0; $i <= 5; $i++) {
            $actual_points  *= $game_eggs_breakers->fetchRandomPoints('common');
        }
        //125 * 11 *  49  = 67375
        $this->assertEquals(67375  ,  $actual_points ); 

        $actual_points_null = $game_eggs_breakers->fetchRandomPoints('common');
        $this->assertNull($actual_points_null);

        // check points pool built
        $this->assertFileExists( $file_pool);
        $this->assertStringEqualsFile( $file_pool, '[]');

        if(file_exists($file_pool)){
            unlink($file_pool);
        }
        if( file_exists($file_expected)) {
            unlink($file_expected);
        }
        
    }


}
