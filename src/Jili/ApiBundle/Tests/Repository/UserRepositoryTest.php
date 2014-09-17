<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class UserRepositoryTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $this->em  = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
       $this->em->close();
    }

    /**
     * @group point_recent
     */
    public function testGetRecentPoint()
    {
        $em = $this->em;
        $date_str = '2014-03-04';
        $result = $em->getRepository('JiliApiBundle:User')->getRecentPoint($date_str);

        $this->assertCount(99, $result);
        $this->assertEquals('565a2bc39cd6621d84173f7ee11ee991',md5(serialize($result)));
    }
    /**
     * @group issue_448
     * @group issue_453 
     */
    public function testCreateOnSignup() {
        $em = $this->em;
        $param = array('email'=>'chiangtor@gmail.com', 'nick'=> 'chiangtor');
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);
        $user = $em->getRepository('JiliApiBundle:User')->createOnSignup($param);
        $this->assertEquals($param['email'], $user->getEmail() );
        $this->assertEquals($param['nick'], $user->getNick() );
        $param [ 'points']=  1;
        $param [ 'isInfoSet']=  1;
        $param [ 'rewardMultiple']=  1;
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);
    }
    /**
     * @group issue_453 
     */
    public function testCreateOnLanding() 
    {
        $em = $this->em;
        $param = array('email'=>'chiangtor@gmail.com', 'nick'=> 'chiangtor');
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);

        // call the create() 
        $param['pwd']='123123';
        $user=$em->getRepository('JiliApiBundle:User')->createOnLanding($param);
        $this->assertEquals($param['email'], $user->getEmail() );
        $this->assertEquals($param['nick'], $user->getNick() );
        $this->assertEmpty($user->getUniqkey());

        // check the create user
        $param [ 'points']=  1;
        $param [ 'isInfoSet']=  1;
        $param [ 'rewardMultiple']=  1;
        unset($param['pwd']);
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);

        // case 2
        $param = array('email'=>'alice.nima@gmail.com', 'nick'=> 'alice32');
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);

        $param['pwd']='123123';
        $param ['uniqkey' ] = '0ce9189316c563fcc9f42047c2a2cf46a0144051';
        $param [ 'isFromWenwen']=  1;
        $user = $em->getRepository('JiliApiBundle:User')->createOnLanding($param);
        $this->assertEquals($param['email'], $user->getEmail() );
        $this->assertEquals($param['nick'], $user->getNick() );
        $this->assertEquals($param['uniqkey'], $user->getUniqkey() );

        // the the result 
        $param [ 'points']=  1;
        $param [ 'isInfoSet']=  1;
        $param [ 'rewardMultiple']=  1;
        unset($param['pwd']);
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);
    }
    /**
     * @group issue_453 
     * @group debug 
     */
    public function testCreateOnWenwen()
    {
        $em = $this->em;
        $param = array('uniqkey'  => '0ce9189316c563fcc9f42047c2a2cf46a0144051', 'email'=>'chiangtor@gmail.com');
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);

        $user = $em->getRepository('JiliApiBundle:User')->createOnWenwen($param);

        $this->assertEquals(0, $user->getPoints());
        $this->assertEquals(0, $user->getIsInfoSet());
        $this->assertEquals(1, $user->getRewardMultiple());
        $this->assertEquals(2, $user->getIsFromWenwen());
        $this->assertEmpty($user->getPwd());
        $this->assertEmpty($user->getNick());

        $this->assertEquals($param['email'], $user->getEmail());
        $this->assertEquals($param['uniqkey'], $user->getUniqkey());
    }
}
