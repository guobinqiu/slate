<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


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
     * @group debug
     * @group issue_448
     */
    public function testCreateOnSignup() {


        $em = $this->em;
        $param = array('email'=>'chiangtor@gmail.com', 'nick'=> 'chiangtor');
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNull($r);
        $em->getRepository('JiliApiBundle:User')->createOnSignup($param);
        $param [ 'points']=  1;
        $param [ 'isInfoSet']=  1;
        $param [ 'rewardMultiple']=  1;
        $r = $em->getRepository('JiliApiBundle:User')->findOneBy($param);
        $this->assertNotNull($r);
    }
}
