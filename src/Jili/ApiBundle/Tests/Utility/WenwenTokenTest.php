<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\WenwenToken;

class WenwenTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group issue_396
     * @group issue_437
     * @group landing
     */
    public function testGetUniqueToken()
    {
        $email = 'aaa@bbb.com';
        $token = WenwenToken::getUniqueToken($email);
        $this->assertEquals('1aa50a441324d893b235bde884afe805de9d8938',$token, ' wenwen token generate');
    }

}
