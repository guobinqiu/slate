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

    /**
     * @group getEntityName
     */
    public function testgetEntityName()
    {
        $className = String::getEntityName('SendMessage',1094121);
        $this->assertEquals('Jili\ApiBundle\Entity\SendMessage01', $className);
    }
}
