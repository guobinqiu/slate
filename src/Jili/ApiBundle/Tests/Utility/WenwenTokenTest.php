<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\WenwenToken;

class WenwenTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group issue_396
     * @group landing
     */
    public function testGetUniqueToken()
    {
        $email = 'aaa@bbb.com';
        $token = WenwenToken::getUniqueToken($email);
        $this->assertEquals('1aa50a441324d893b235bde884afe805de9d8938',$token, ' wenwen token generate');
    }

    /**
     * @group issue_437
     */
    public function testVoteLinkUniqueToken()
    {
        $user_id= 1;
        $token = WenwenToken::getUniqueToken($user_id);
        $this->assertEquals('45a53d4a8d954be13cd258578da54cab4730184b',$token, ' vote api link token generate');
    }
}
