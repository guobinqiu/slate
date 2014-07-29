<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\WenwenToken;

class WenwenTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group issue_396
     * @group landing
     */
    public function testGetEmailToken()
    {
        $email = 'aaa@bbb.com';
        $token = WenwenToken::getEmailToken($email);
        $this->assertEquals('1aa50a441324d893b235bde884afe805de9d8938',$token, ' wenwen token generate');
    }
}
