<?php
namespace Jili\BackendBundle\Tests\Utility;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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
        $this->assertNull(  TaobaoOrderToEggs::lessToNext(50.01));

        $this->assertEquals( 0.01, TaobaoOrderToEggs::lessToNext(9.99));
    }
}
