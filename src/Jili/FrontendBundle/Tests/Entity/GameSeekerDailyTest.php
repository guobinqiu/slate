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
     * @group debug
     * @group issue-524 
     */
    public function testCreate()
    {
        $entity = new GameSeekerDaily();
        $this->assertEquals(0, $entity->getPoints(), 'default point is 0');
        $entity->setToken('12345678901234567890123456789012');
        $this->assertEquals('12345678901234567890123456789012', $entity->getToken(), '32 byte token');

        $entity->setUserId(10);
        $entity->setTokenUpdatedAt( new \DateTime('2014-11-07 10:19:48') ); 
        $entity->setToken();
        $p= new \DateTime('2014-11-07 10:19:48') ;
        $ts = $entity->getTokenUpdatedAt()->getTimestamp();
        $expected = md5('10'. $p. $ts);
        $this->assertEquals($expected, $this->getToken(),'with empty');

        $entity->setUserId(2030);
        $entity->setTokenUpdatedAt( new \DateTime('2014-12-07 10:19:48') ); 
        $entity->setToken();
        $p= new \DateTime('2014-12-07 10:19:48') ;
        $ts = $entity->getTokenUpdatedAt()->getTimestamp();
        $expected = md5('abc2030'. $p. $ts);
        $this->assertEquals($expected, $this->getToken('abc'),'with length < 16');

        $entity->setUserId(13430);
        $entity->setTokenUpdatedAt( new \DateTime('2014-12-07 10:19:48') ); 
        $entity->setToken();
        $p= new \DateTime('2014-12-07 10:19:48') ;
        $ts = $entity->getTokenUpdatedAt()->getTimestamp();
        $expected = md5('1234567890abcdef1234567890abcde1234567890abcde13430'. $p. $ts);
        $this->assertEquals($expected, $this->getToken('1234567890abcdef1234567890abcde1234567890abcde'),'with length > 16');
    }
}

