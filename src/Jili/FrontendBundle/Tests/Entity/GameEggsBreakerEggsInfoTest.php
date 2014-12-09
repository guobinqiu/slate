<?php
namespace Jili\FrontendBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;

class GameEggsBreakerEggsInfoTest extends KernelTestCase
{
    
    /**
     * @group issue_537
     * @group debug 
     */
    public function testGetLessForNextEgg()
    {
        $entity = new GameEggsBreakerEggsInfo();
        $this->assertEquals(10.00, GameEggsBreakerEggsInfo::COST_PER_EGG);

        $entity->setOffcutForNext(100.00);
        $this->assertEquals(0, $entity->getLessForNextEgg() );

        $entity->setOffcutForNext(3.00);
        $this->assertEquals(7.00, $entity->getLessForNextEgg() );

        $entity->setOffcutForNext(0.01);
        $this->assertEquals(9.99, $entity->getLessForNextEgg() );

        $entity->setOffcutForNext(9.99);
        $this->assertEquals(0.01, $entity->getLessForNextEgg() );
    }
}

