<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\String;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testUidAdid()
    {

        $s = String::buildUidAdid(0,1);
        $this->assertEquals( '0a1',$s);

        $b = String::explodeUidAdid($s);
        $this->assertEquals(2, count($b));
        $this->assertArrayHasKey('uid', $b);
        $this->assertEquals(0, $b['uid']);
        $this->assertArrayHasKey('adid', $b);
        $this->assertEquals(1, $b['adid']);

        $s = String::buildUidAdid(0,0);
        $this->assertEquals( '0a0',$s);
        $b = String::explodeUidAdid($s);
        $this->assertEquals(2, count($b));
        $this->assertArrayHasKey('uid', $b);
        $this->assertEquals(0, $b['uid']);
        $this->assertArrayHasKey('adid', $b);
        $this->assertEquals(0, $b['adid']);


        $s = String::buildUidAdid(0,'');
        $this->assertEquals( '0a0',$s);
        $b = String::explodeUidAdid($s);
        $this->assertEquals(2, count($b));
        $this->assertArrayHasKey('uid', $b);
        $this->assertEquals(0, $b['uid']);
        $this->assertArrayHasKey('adid', $b);
        $this->assertEquals(0, $b['adid']);



        $s = String::buildUidAdid(0,null);
        $this->assertEquals( '0a0',$s);
        $b = String::explodeUidAdid($s);
        $this->assertEquals(2, count($b));
        $this->assertArrayHasKey('uid', $b);
        $this->assertEquals(0, $b['uid']);
        $this->assertArrayHasKey('adid', $b);
        $this->assertEquals(0, $b['adid']);



        $s = String::buildUidAdid(0,'a');
        $this->assertEquals( '0a0',$s);
        $b = String::explodeUidAdid($s);
        $this->assertEquals(2, count($b));
        $this->assertArrayHasKey('uid', $b);
        $this->assertEquals(0, $b['uid']);
        $this->assertArrayHasKey('adid', $b);
        $this->assertEquals(0, $b['adid']);
    }

    public function test_parseChanetCallbackUrl()
    {
        $this->assertEquals($expected=array('user_id' => '123123',
            'advertiserment_id' =>'78979'), String::parseChanetCallbackUrl('123123_78979','123123'), 'array as [ user_id, advertiserment_id ] should be return');
        $this->assertNull( String::parseChanetCallbackUrl('123123_78979','12312' ), 'null should return');
        $this->assertNull( String::parseChanetCallbackUrl('123123_78979','123123_' ), 'null should return ');
    }

}
