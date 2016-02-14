<?php

use \SOPx\Auth\V1_1\Util;

class SOPx_Auth_V1_1_UtilTest extends \PHPUnit_Framework_TestCase {

    public function testCreateStringFromArray() {
        $this->assertSame(
            'xxx=xxx&yyy=yyy&zzz=zzz',
            Util::createStringFromArray(array(
                'zzz' => 'zzz',
                'yyy' => 'yyy',
                'xxx' => 'xxx',
            ))
        );
    }

    public function testCreateStringFromArray_ignores_by_prefix() {
        $this->assertSame(
            'xxx=xxx&yyy=yyy&zzz=zzz',
            Util::createStringFromArray(array(
                'sop_xxx' => 'xxx',
                'zzz' => 'zzz',
                'yyy' => 'yyy',
                'xxx' => 'xxx',
            ))
        );
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function testCreateStringFromArray_fail_on_non_scalar_found() {
        Util::createStringFromArray(array(
            'hoge' => array(
                'aaa' => 'aaa',
            ),
        ));
    }

    public function testCreateSignature_from_array() {
        $this->assertSame(
            '2fbfe87e54cc53036463633ef29beeaa4d740e435af586798917826d9e525112',
            Util::createSignature(
                array(
                    'ccc' => 'ccc',
                    'bbb' => 'bbb',
                    'aaa' => 'aaa',
                ),
                'hogehoge'
            )
        );
    }

    public function testCreateSignature_from_array_with_ignored_prefix() {
        $this->assertSame(
            '2fbfe87e54cc53036463633ef29beeaa4d740e435af586798917826d9e525112',
            Util::createSignature(
                array(
                    'sop_sss' => 'sss',
                    'ccc' => 'ccc',
                    'bbb' => 'bbb',
                    'aaa' => 'aaa',
                ),
                'hogehoge'
            )
        );
    }

    public function testCreateSignature_from_scalar() {
        $this->assertSame(
            'dc76e675e2bcabc31182e33506f5b01ea7966a9c0640d335cc6cc551f0bb1bba',
            Util::createSignature(
                '{"hoge":"fuga"}',
                'hogehoge'
            )
        );
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function testCreateSignature_fail_on_incompatible_type() {
        Util::createSignature(
            new stdClass(),
            'hogehoge'
        );
    }

    public function testIsSignatureValid_on_missing_time() {
        $params = array(
            'aaa' => 'aaa',
            'bbb' => 'bbb',
        );
        $sig = Util::createSignature($params, 'hogehoge');

        $this->assertFalse(
            Util::isSignatureValid($sig, $params, 'hogehoge')
        );
    }

    public function testIsSignatureValid_on_too_old_time() {
        $now = 100000;
        $time = $now - Util::$SIG_VALID_FOR_SEC - 1;
        $params = array(
            'aaa' => 'aaa',
            'bbb' => 'bbb',
            'time' => $time,
        );
        $sig = Util::createSignature($params, 'hogehoge');

        $this->assertFalse(
            Util::isSignatureValid($sig, $params, 'hogehoge', $now)
        );
    }

    public function testIsSignatureValid_on_lower_limit_time() {
        $now = 100000;
        $time = $now - Util::$SIG_VALID_FOR_SEC;
        $params = array(
            'aaa' => 'aaa',
            'bbb' => 'bbb',
            'time' => $time,
        );
        $sig = Util::createSignature($params, 'hogehoge');

        $this->assertTrue(
            Util::isSignatureValid($sig, $params, 'hogehoge', $now)
        );
    }

    public function testIsSignatureValid_on_invalid_sig() {
        $now = 100000;
        $time = $now;
        $params = array(
            'aaa' => 'aaa',
            'bbb' => 'bbb',
            'time' => $time,
        );
        $sig = Util::createSignature($params, 'hogehoge');

        $this->assertFalse(
            Util::isSignatureValid($sig. "x", $params, 'hogehoge', $now)
        );
    }

    public function testIsSignatureValid_on_upper_limit_time() {
        $now = 100000;
        $time = $now + Util::$SIG_VALID_FOR_SEC;
        $params = array(
            'aaa' => 'aaa',
            'bbb' => 'bbb',
            'time' => $time,
        );
        $sig = Util::createSignature($params, 'hogehoge');

        $this->assertTrue(
            Util::isSignatureValid($sig, $params, 'hogehoge', $now)
        );
    }

    public function testIsSignatureValid_on_too_new_time() {
        $now = 100000;
        $time = $now + Util::$SIG_VALID_FOR_SEC + 1;
        $params = array(
            'aaa' => 'aaa',
            'bbb' => 'bbb',
            'time' => $time,
        );
        $sig = Util::createSignature($params, 'hogehoge');

        $this->assertFalse(
            Util::isSignatureValid($sig, $params, 'hogehoge', $now)
        );
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function testIsSignatureValid_on_malformed_json() {
        Util::isSignatureValid('signature', '{"mal":"formed"', 'hogehoge', '1234');
    }
}
