<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\CurlUtil;

class CurlUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group issue_682
     */
    public function test_curl()
    {
        try {
            $return = CurlUtil :: curl('http://www.91jili.com/');
        } catch (\ Exception $e) {
            $return = $e->getMessage();
        }
        $this->assertContains('积粒网', $return);
    }
}
