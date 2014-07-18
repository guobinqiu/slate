<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\SequenseEntityClassFactory;

class ClassNameTest extends \ PHPUnit_Framework_TestCase {

    /**
    * @group getClassName
    */
    public function testgetClassName() {
        $sendMessage = SequenseEntityClassFactory :: getClassName('SendMessage', 1094121);
        $this->assertEquals('Jili\ApiBundle\Entity\SendMessage01', get_class($sendMessage));
    }
}
?>