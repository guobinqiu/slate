<?php
namespace Jili\FrontendBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Jili\FrontendBundle\Entity\GameEggsBreakerTaobaoOrder;

class GameEggsBreakerTaobaoOrderTest extends KernelTestCase
{

    /**
     * @group issue-537
     */
    public function testInsertUserPost()
    {
        $entity = new GameEggsBreakerTaobaoOrder();

        $this->assertEquals( '', $entity->getAuditBy());
        $this->assertEquals(0, $entity->getAuditStatus());
        $this->assertEquals(0, $entity->getIsValid());
        $this->assertEquals(0, $entity->getIsEgged());

    }
}

