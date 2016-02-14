<?php

use \Net_URL2;
use \SOPx\Auth\V1_1\Util;
use \SOPx\Auth\V1_1\Request\POSTJSON;

class SOPx_Auth_V1_1_Request_POSTJSONTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->uri = new \Net_URL2('http://www.researchpanelasia.com');
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function testCreateRequest_fails_on_missing_time()
    {
        POSTJSON::createRequest(
            $this->uri,
            array( 'aaa' => 'aaa' ),
            'hogehoge'
        );
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function testCreateRequest_fails_on_missing_app_secret()
    {
        POSTJSON::createRequest(
            $this->uri,
            array( 'aaa' => 'aaa', 'time' => '1234', ),
            ''
        );
    }

    public function testCreateRequest()
    {
        $req = POSTJSON::createRequest(
            $this->uri,
            array(
                'time' => '1234',
                'bbb' => 'bbb',
                'aaa' => 'aaa',
            ),
            'hogehoge'
        );

        $uri = new \Net_URL2($req->uri);

        $this->assertEquals('POST', $req->method);
        $this->assertEquals($this->uri->getURL(), $uri->getURL());
        $this->assertEquals($req->content_type, 'application/json');
        $this->assertEquals(
            array(
                'aaa' => 'aaa',
                'bbb' => 'bbb',
                'time' => '1234',
            ),
            json_decode($req->payload, true)
        );
        $this->assertEquals(
            Util::createSignature($req->payload, 'hogehoge'),
            $req->headers['X-Sop-Sig']
        );
    }
}
