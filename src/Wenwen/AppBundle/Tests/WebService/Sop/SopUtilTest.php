<?php

namespace Wenwen\AppBundle\Tests\WebService\Sop;

use Wenwen\AppBundle\WebService\Sop\SopUtil;

class SopUtilTest extends \PHPUnit_Framework_TestCase
{

    public function test_getJsopURL()
    {
        $app_sop_host = 'www.test.com';

        $actual = SopUtil::getJsopURL(array (
            'app_id' => '1',
            'app_mid' => '2',
            'sig' => '3',
            'time' => '4',
            'sop_callback' => 'callback5'
        ), $app_sop_host);
        $expected = 'https://' . $app_sop_host . '/api/v1_1/surveys/js?' . 'app_id=1&app_mid=2&sig=3&time=4&sop_callback=callback5';
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Insuffucient parameter
     */
    public function test_getJsopURLWithError()
    {
        $app_sop_host = 'www.test.com';

        $actual = SopUtil::getJsopURL(array (
            'app_id' => '1',
            'app_mid' => '2',
            'sig' => '3',
            'sop_callback' => 'callback5'
        ), $app_sop_host);
        $expected = 'https://' . $app_sop_host . '/api/v1_1/surveys/js?' . 'app_id=1&app_mid=2&sig=3&time=4&sop_callback=callback5';
        $this->assertEquals($expected, $actual);
    }
}