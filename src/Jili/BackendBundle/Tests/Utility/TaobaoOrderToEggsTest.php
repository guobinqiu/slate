<?php
namespace Jili\BackendBundle\Tests\Utility;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Jili\BackendBundle\Utility\TaobaoOrderToEggs;

class TaobaoOrderToEggsTest  extends KernelTestCase
{

    /**
     * @group issue_537
     */
    public function testCaculateEggs() 
    {
        $return = TaobaoOrderToEggs::caculateEggs(0);
        $this->assertEquals( 0, $return['left'] );
        $this->assertEquals( 0, $return['count_of_eggs'] );

        $fixtues = array(
            array(  'input'=>0.01 , 'expects'=> array( 0.01, 0) ),
            array(  'input'=>9.99 , 'expects'=> array( 9.99, 0) ),
            array(  'input'=>10.01 , 'expects'=> array( 0.01, 1) ),
            array(  'input'=>19.99 , 'expects'=> array( 9.99, 1) ),
            array(  'input'=>20.01 , 'expects'=> array( 0.01, 2) ),
            array(  'input'=>29.99 , 'expects'=> array( 9.99, 2) ),
            array(  'input'=>30.01 , 'expects'=> array( 10.01, 2) ),
            array(  'input'=>39.99 , 'expects'=> array( 19.99, 2) ),
            array(  'input'=>40.01 , 'expects'=> array( 20.01, 2) ),
            array(  'input'=>49.99 , 'expects'=> array( 29.99, 2) ),
            array(  'input'=>50.01 , 'expects'=> array( 0.01, 3) ),
            array(  'input'=>59.99 , 'expects'=> array( 9.99, 3) ),
            array(  'input'=>60.01 , 'expects'=> array( 10.01, 3) ),
            array(  'input'=>69.99 , 'expects'=> array( 19.99, 3) ),
            array(  'input'=>70.01 , 'expects'=> array( 20.01, 3) ),
            array(  'input'=>79.99 , 'expects'=> array( 29.99, 3) ),
            array(  'input'=>80.01 , 'expects'=> array( 30.01, 3) ),
            array(  'input'=>89.99 , 'expects'=> array( 39.99, 3) ),
            array(  'input'=>90.01 , 'expects'=> array( 40.01, 3) ),
            array(  'input'=>99.99 , 'expects'=> array( 49.99, 3) ),
            array(  'input'=>100.01 , 'expects'=> array( 0.01, 4) ),
            array(  'input'=>109.99 , 'expects'=> array( 9.99, 4) ),
            array(  'input'=>110.01 , 'expects'=> array( 10.01, 4) ),
            array(  'input'=>119.99 , 'expects'=> array( 19.99, 4) ),
            array(  'input'=>120.01 , 'expects'=> array( 20.01, 4) ),
            array(  'input'=>129.99 , 'expects'=> array( 29.99, 4) ),
            array(  'input'=>130.01 , 'expects'=> array( 30.01, 4) ),
            array(  'input'=>139.99 , 'expects'=> array( 39.99, 4) ),
            array(  'input'=>140.01 , 'expects'=> array( 40.01, 4) ),
            array(  'input'=>149.99 , 'expects'=> array( 49.99, 4) ),
            array(  'input'=>150.01 , 'expects'=> array( 0.01, 5) ),
            array(  'input'=>159.99 , 'expects'=> array( 9.99, 5) ),
            array(  'input'=>160.01 , 'expects'=> array( 10.01, 5) ),
            array(  'input'=>169.99 , 'expects'=> array( 19.99, 5) ),
            array(  'input'=>170.01 , 'expects'=> array( 20.01, 5) ),
            array(  'input'=>179.99 , 'expects'=> array( 29.99, 5) ),
            array(  'input'=>180.01 , 'expects'=> array( 30.01, 5) ),
            array(  'input'=>189.99 , 'expects'=> array( 39.99, 5) ),
            array(  'input'=>200.01 , 'expects'=> array( 0.01, 6) ),
            array(  'input'=>209.99 , 'expects'=> array( 9.99, 6) ),
            array(  'input'=>240.01 , 'expects'=> array( 40.01, 6) ),
            array(  'input'=>249.99 , 'expects'=> array( 49.99, 6) )
        );

        foreach ( $fixtues as $row ) {
            $return = TaobaoOrderToEggs::caculateEggs( $row['input']);
            $this->assertEquals($row['expects'][0], $return['left'] , $row['input'] );
            $this->assertEquals( $row['expects'][1], $return['count_of_eggs'] , $row['input']);
        }

    }
    /**
     * @group issue_537
     */
    public function testLessToNext() 
    {
        $this->assertEquals( 10, TaobaoOrderToEggs::lessToNext(0));
        $this->assertEquals( 9.99, TaobaoOrderToEggs::lessToNext(0.01));
        $this->assertEquals( 9.99, TaobaoOrderToEggs::lessToNext(10.01));
        $this->assertEquals( 29.99, TaobaoOrderToEggs::lessToNext(20.01));
        $this->assertEquals( 19.99, TaobaoOrderToEggs::lessToNext(30.01));
        $this->assertEquals( 9.99, TaobaoOrderToEggs::lessToNext(40.01));
        $this->assertEquals( 49.99, TaobaoOrderToEggs::lessToNext(50.01));
        $this->assertEquals( 0.01, TaobaoOrderToEggs::lessToNext(9.99));
        $this->assertEquals( 49.99, TaobaoOrderToEggs::lessToNext(100.01));
        $this->assertEquals(0.01 , TaobaoOrderToEggs::lessToNext(149.99));
        $this->assertEquals(49.99 , TaobaoOrderToEggs::lessToNext(150.01));
        $this->assertEquals(19.99 , TaobaoOrderToEggs::lessToNext(180.01));
        $this->assertEquals(49.99 , TaobaoOrderToEggs::lessToNext(200.01));
    }

    /**
     * @group issue_592 
     */
    public function testCaculateImmediateEggs()
    {

        $this->assertEquals(array('left'=> 0, 'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs( -1, 0,10));
        $this->assertEquals(array('left'=> 0, 'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs( 0, 0,10));
        $this->assertEquals(array('left'=> 0, 'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs( 1, 0,0));
        $this->assertEquals(array('left'=> 0, 'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs( 1, 0,-10));

        $this->assertEquals(array('left'=> 0.01,  'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs(0.01 ,0,10));
        $this->assertEquals(array('left'=> 1.11, 'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs(1.11 ,0, 10));
        $this->assertEquals(array('left'=> 9.99, 'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs( 9.99,0, 10));
        $this->assertEquals(array('left'=> 0.01, 'count_of_eggs'=>1 ) , TaobaoOrderToEggs::caculateImmediateEggs( 10.01,0 ,10));
        $this->assertEquals(array('left'=> 9.99, 'count_of_eggs'=>1 ) , TaobaoOrderToEggs::caculateImmediateEggs( 19.99,0 ,10));


        $this->assertEquals(array('left'=> 0.01,  'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs(0.01 ,10,10));
        $this->assertEquals(array('left'=> 1.11, 'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs(1.11 ,10, 10));
        $this->assertEquals(array('left'=> 9.99, 'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs( 9.99,10, 10));
        $this->assertEquals(array('left'=> 0.01, 'count_of_eggs'=>1 ) , TaobaoOrderToEggs::caculateImmediateEggs( 10.01,10 ,10));
        $this->assertEquals(array('left'=> 9.99, 'count_of_eggs'=>1 ) , TaobaoOrderToEggs::caculateImmediateEggs( 19.99,10 ,10));

        $this->assertEquals(array('left'=> 0.03,  'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs(0.01 ,9.98, 10));
        $this->assertEquals(array('left'=> 9.97,  'count_of_eggs'=>1 ) , TaobaoOrderToEggs::caculateImmediateEggs(9.98 ,0.01,10));
        $this->assertEquals(array('left'=> 0.02,  'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs(0.01 ,9.99,10));
        $this->assertEquals(array('left'=> 0.03,  'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs(0.02 ,9.99,10));
        $this->assertEquals(array('left'=> 0.01,  'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs(0.01 ,10,10));

        $this->assertEquals(array('left'=> 0.01,  'count_of_eggs'=>1 ) , TaobaoOrderToEggs::caculateImmediateEggs(0.02 ,0.01, 10));

        $this->assertEquals(array('left'=> 0.01,  'count_of_eggs'=>2 ) , TaobaoOrderToEggs::caculateImmediateEggs(10.02 ,0.01, 10));

        $this->assertEquals(array('left'=> 9.99,  'count_of_eggs'=>0 ) , TaobaoOrderToEggs::caculateImmediateEggs(0.01 ,0.02, 10));

    }
}