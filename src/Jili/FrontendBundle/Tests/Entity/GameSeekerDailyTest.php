<?php
namespace Jili\FrontendBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#use Doctrine\Common\DataFixtures\Purger\ORMPurger;
#use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
#use Doctrine\Common\DataFixtures\Loader;

use Jili\FrontendBundle\Entity\GameSeekerDaily;

class GameSeekerDailyTest extends KernelTestCase
{

    /**
     * @group issue-524 
     */
    public function testCreate()
    {
        $entity = new GameSeekerDaily();
        $this->assertEquals(-1, $entity->getPoints(), 'default point is 0');
        $this->assertEquals(date('Y-m-d'), $entity->getClickedDay()->format('Y-m-d'));
        $this->assertEquals(date('Y-m-d 00:00:00'),$entity->getClickedDay()->format('Y-m-d H:i:s'));
        $entity->setToken('12345678901234567890123456789012');
        $this->assertEquals('12345678901234567890123456789012', $entity->getToken(), '32 byte token');

        $entity->setUserId(10);
        $entity->setTokenUpdatedAt( new \DateTime('2014-11-07 10:19:48') ); 
        $entity->setToken();
        $p= new \DateTime('2014-11-07 10:19:48') ;
        $ts = $entity->getTokenUpdatedAt()->getTimestamp();
        $expected = md5('10'. $p->getTimestamp() . $ts);
        $this->assertEquals($expected, $entity->getToken(),'with empty');

        $entity->setUserId(2030);
        $entity->setTokenUpdatedAt( new \DateTime('2014-12-07 10:19:48') ); 
        $entity->setToken('abc');

        $p= new \DateTime('2014-12-07 10:19:48') ;

        $ts = $entity->getTokenUpdatedAt()->getTimestamp();

        $expected = md5('abc2030'. $p->getTimestamp() . $ts);
        $this->assertEquals($expected, $entity->getToken(),'with length < 16');

        $entity->setUserId(13430);
        $entity->setTokenUpdatedAt( new \DateTime('2014-12-07 10:19:48') ); 
        $entity->setToken('1234567890abcdef1234567890abcde1234567890abcde');
        $p= new \DateTime('2014-12-07 10:19:48') ;
        $ts = $entity->getTokenUpdatedAt()->getTimestamp();
        $expected = md5('1234567890abcdef1234567890abcde1234567890abcde13430'. $p->getTimestamp(). $ts);
        $this->assertEquals($expected, $entity->getToken(),'with length > 16');
    }
}

