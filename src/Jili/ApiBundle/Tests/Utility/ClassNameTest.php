<?php
namespace Jili\ApiBundle\Tests\Utility;

use Jili\ApiBundle\Utility\ClassNameFactory;

class ClassNameTest extends \ PHPUnit_Framework_TestCase {

    /**
    * @group getClassName
    */
    public function testCreate() {
        $classNameFactory = ClassNameFactory :: create('SendMessage', 1094121);
        $className = $classNameFactory->getClassName();
        $this->assertEquals('SendMessage01', $className);
    }
}
?>