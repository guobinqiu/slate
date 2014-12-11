<?php
namespace Jili\FrontendBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Jili\FrontendBundle\Entity\GameEggsBreakerEggsInfo;

class GameEggsBreakerEggsInfoTest extends KernelTestCase
{
    
    /**
     * @group issue_537
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

    /**
     * @group issue_537
     */
    public function testUpdateNumOfEggs() 
    {
//$eggs, $token 
        $entity = new GameEggsBreakerEggsInfo();

        $eggs = array('offcut'=> 0.01 ,'common'=> 3, 'consolation'=> 7);

        $actual = $entity->updateNumOfEggs($eggs, '');
        $this->assertNull( $actual);

        $token = $entity->getToken();
        $actual = $entity->updateNumOfEggs($eggs, $token);
        $this->assertNotNull( $actual);
        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerEggsInfo',$actual);
        $this->assertEquals( 3, $entity->getNumOfCommon());
        $this->assertEquals( 7, $entity->getNumOfConsolation());
        $this->assertEquals( 0.01, $entity->getOffcutForNext());
        $this->assertEquals( $token, $entity->getToken());
        
        $eggs = array('offcut'=> 5.0, 'common'=> 11, 'consolation'=> 37);

        $actual = $entity->updateNumOfEggs($eggs, $token)
            ->refreshToken();

        $this->assertInstanceOf('\\Jili\\FrontendBundle\\Entity\\GameEggsBreakerEggsInfo',$actual);
        $this->assertEquals( 14, $entity->getNumOfCommon());
        $this->assertEquals( 44, $entity->getNumOfConsolation());
        $this->assertEquals( 5.0, $entity->getOffcutForNext());
        $this->assertNotEquals($token, $entity->getToken());
    }
}

