<?php

use \SOPx\Auth\V1_1\Client;

class SOPx_Auth_V1_1_ClientTest extends \PHPUnit_Framework_TestCase {

    protected $auth;

    protected function setUp() {
        $this->auth = new \SOPx\Auth\V1_1\Client('1234', 'hogefuga');
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function testNew_fails_if_app_id_is_missing() {
        $auth = new \SOPx\Auth\V1_1\Client(null, 'hoge');
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function testNew_fails_if_app_secret_is_missing() {
        $auth = new \SOPx\Auth\V1_1\Client(1, null);
    }

    public function testNew_succeeds() {
        $auth = new \SOPx\Auth\V1_1\Client('1', 'hogehoge');

        $this->assertEquals('1', $auth->getAppId());
        $this->assertEquals('hogehoge', $auth->getAppSecret());
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function getCreateRequest_on_invalid_method() {
        $this->auth->createRequest(
            'PUT', 'http://hoge/', array('aaa' => 'aaa')
        );
    }

    public function testCreateRequest_on_GET() {
        $req = $this->auth->createRequest(
            'GET', 'http://hoge/', array('aaa' => 'aaa')
        );

        $this->assertInstanceOf('Httpful\Request', $req);
        $this->assertEquals('GET', $req->method);
    }

    public function testCreateRequest_on_POST() {
        $req = $this->auth->createRequest(
            'POST', 'http://hoge/', array('aaa' => 'aaa')
        );

        $this->assertInstanceOf('Httpful\Request', $req);
        $this->assertEquals('POST', $req->method);
    }

    public function testCreateRequest_on_POSTJSON() {
        $req = $this->auth->createRequest(
            'POSTJSON', 'http://hoge/', array('aaa' => 'aaa')
        );

        $this->assertInstanceOf('Httpful\Request', $req);
        $this->assertEquals('POST', $req->method);
        $this->assertRegExp('/\A[0-9a-f]{64}\z/', $req->headers['X-Sop-Sig']);
    }

    public function testVerifySignature_on_array() {
        $req = $this->auth->createRequest(
            'POST', 'http://hoge/', array('aaa' => 'aaa')
        );

        $sig = $req->payload['sig'];
        unset($req->payload['sig']);

        $this->assertTrue(
            $this->auth->verifySignature(
                $sig,
                $req->payload
            )
        );
    }

    public function testVerifySignature_on_JSON() {
        $req = $this->auth->createRequest(
            'POSTJSON', 'http://hoge/', array('aaa' => 'aaa')
        );

        $sig = $req->headers['X-Sop-Sig'];

        $this->assertTrue(
            $this->auth->verifySignature(
                $sig,
                $req->payload
            )
        );
    }
}
