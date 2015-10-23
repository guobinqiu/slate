<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\PasswordEncoder;

class PasswordEncoderTest extends \PHPUnit_Framework_TestCase
{
    /**
    * @group createInstance
    */
    public function testEncode()
    {
        $encoded = PasswordEncoder::encode( 'blowfish','111111','★★★★★アジア事業戦略室★★★★★' );
        $this->assertEquals('aPaR9Ucsu4U=', $encoded, ' blowfisn encoded 123123');
    }
}

