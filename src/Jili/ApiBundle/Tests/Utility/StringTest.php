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

    public function test_encodeForCommandArgument( ) 
    {

        $data = array(    'name1'=>'Jarod',
            'email'=>'chiangtor@gmail.com',
            'title'=>'test',
            'survey_title'=>'survey_title_test',
            'survey_point'=>'101'  );

        $this->assertEquals('eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3JAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3QiLCJzdXJ2ZXlfcG9pbnQiOiIxMDEifQ==', String::encodeForCommandArgument($data ), 'array to json , then base64');
    }

    public function test_dencodeForCommandArgument() 
    {
        $string = 'eyJuYW1lMSI6Ikphcm9kIiwiZW1haWwiOiJjaGlhbmd0b3JAZ21haWwuY29tIiwidGl0bGUiOiJ0ZXN0Iiwic3VydmV5X3RpdGxlIjoic3VydmV5X3RpdGxlX3Rlc3QiLCJzdXJ2ZXlfcG9pbnQiOiIxMDEifQ==';
        $data = array(    'name1'=>'Jarod',
            'email'=>'chiangtor@gmail.com',
            'title'=>'test',
            'survey_title'=>'survey_title_test',
            'survey_point'=>'101'  );
        $this->assertEquals($data, String::decodeForCommandArgument($string), ' string to array');
    }
}
