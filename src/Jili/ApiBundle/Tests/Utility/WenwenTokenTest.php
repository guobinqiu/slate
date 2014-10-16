<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\WenwenToken;

class WenwenTokenTest extends \PHPUnit_Framework_TestCase {

    /**
     * @group issue_396
     * @group issue_437
     * @group landing
     */
    public function testGetUniqueToken() {
        $email = 'aaa@bbb.com';
        $token = WenwenToken :: getUniqueToken($email);
        $this->assertEquals('1aa50a441324d893b235bde884afe805de9d8938', $token, ' wenwen token generate');
    }

    /**
     * @group issue_487
     */
    public function testCreateStringFromArray() {
        $time = time();
        $params = array (
            'cross_id' => 1,
            'time' => $time
        );
        $string = WenwenToken :: createStringFromArray($params);
        $this->assertEquals('cross_id=1&time=' . $time, $string);
    }

    /**
     * @group issue_487
     */
    public function testCreateSignature() {
        $time = time();
        $params = array (
            'cross_id' => 1,
            'time' => $time
        );
        $secret_key = 'ADF93768CF';
        $signature = WenwenToken :: createSignature($params, $secret_key);
        $this->assertEquals(64, strlen($signature));
    }

    /**
     * @group issue_487
     */
    public function testIsSignatureValid() {
        $time = time();
        $params = array (
            'cross_id' => 1,
            'time' => $time
        );
        $secret_key = 'ADF93768CF';
        $signature = WenwenToken :: createSignature($params, $secret_key);
        $return = WenwenToken :: isSignatureValid($signature, $params, $secret_key);
        $this->assertTrue($return);

        $return = WenwenToken :: isSignatureValid(null, $params, $secret_key);
        $this->assertFalse($return);
        $return = WenwenToken :: isSignatureValid($signature, null, $secret_key);
        $this->assertFalse($return);
        $return = WenwenToken :: isSignatureValid($signature, $params, null);
        $this->assertFalse($return);

        $params = array (
            'cross_id' => 1
        );
        $return = WenwenToken :: isSignatureValid($signature, $params, $secret_key);
        $this->assertFalse($return);

        $params = array (
            'cross_id' => 1,
            'time' => $time - 400
        );
        $return = WenwenToken :: isSignatureValid($signature, $params, $secret_key, $time);
        $this->assertFalse($return);

        $params = array (
            'cross_id' => 1,
            'time' => $time + 400
        );
        $return = WenwenToken :: isSignatureValid($signature, $params, $secret_key, $time);
        $this->assertFalse($return);
    }

    /**
     * @group issue_487
     */
    public function testGenerateOnetimeToken() {
        $token = WenwenToken :: generateOnetimeToken();
        $this->assertTrue(isset ($token));
        $this->assertEquals(64, strlen($token));
    }

}