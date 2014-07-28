<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\SequenseEntityClassFactory;

class CreateInstanceTest extends \ PHPUnit_Framework_TestCase {

    /**
    * @group createInstance
    */
    public function testcreateInstance() {
        $sendMessage = SequenseEntityClassFactory :: createInstance('SendMessage', 1094121);
        $this->assertEquals('Jili\ApiBundle\Entity\SendMessage01', get_class($sendMessage));
    }
}
?>
