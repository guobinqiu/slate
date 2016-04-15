<?php

namespace Wenwen\AppBundle\Tests\Entity;

class SsiRespondentTest extends \PHPUnit_Framework_TestCase
{
    public function testParseRespondentId()
    {
        $this->assertSame('10', \Wenwen\AppBundle\Entity\SsiRespondent::parseRespondentId('wwcn-10'));
        $this->assertNull(\Wenwen\AppBundle\Entity\SsiRespondent::parseRespondentId('pnkr-10'));
        $this->assertNull(\Wenwen\AppBundle\Entity\SsiRespondent::parseRespondentId('10'));
    }
}
