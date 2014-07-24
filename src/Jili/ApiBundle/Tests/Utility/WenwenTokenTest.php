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
        WenwenToken::getEmailToken($email);       

        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $this->assertEquals(1,1, 'todo: wenwen token generate');

    }
}

