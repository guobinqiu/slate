<?php
namespace Jili\FrontendBundle\Tests\Entity;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;
use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;

class GameEggsBreakerTaobaoOrderTest extends KernelTestCase
{

    /**
     * @group issue_537
     */
    public function testCreate()
    {
        $entity = new GameEggsBreakerTaobaoOrder();
        $this->assertEquals( '', $entity->getAuditBy());
        $this->assertEquals(0, $entity->getAuditStatus());
        $this->assertEquals(0, $entity->getIsValid());
        $this->assertEquals(0, $entity->getIsEgged());
    }


}

