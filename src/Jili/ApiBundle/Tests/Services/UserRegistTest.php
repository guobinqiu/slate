<?php
namespace Jili\ApiBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
//use Jili\ApiBundle\DataFixtures\ORM\LoadLandingTracerCodeData;

class UserRegistTest extends KernelTestCase
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
        $container  = static::$kernel->getContainer();
//        $log_path = $container->getParameter('kernel.logs_dir');
//        $log_path .= '/../logs_data/'.$container->getParameter('kernel.environment');
//        $log_path .= '.user_source.log';

//        if( file_exists( $log_path)) {
//            @unlink( $log_path);
//        }
//        $this->log_path = $log_path;
        $this->container =  $container;
        $this->em = $em;
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
     * @group issue_474
     */
    public function testqq_user_regist() 
    {
        $params = array('email'=>'alice_nima@gmail.com', 'open_id'=>'973F697E97A60289C8C455B1D65FF5F0', 'pwd'=>'123qwe');
        $qqUser =  $this->container->get('user_regist')->qq_user_regist($params);

        // 

        $params = array('nick'=>'alice32','email'=>'alice_nima@gmail.com', 'open_id'=>'973F697E97A60289C8C455B1D65FF5F0', 'pwd'=>'123qwe');
        $qqUser =  $this->container->get('user_regist')->qq_user_regist($params);

        $user = $this->em->getRepository('JiliApiBundle:User')->findOneByEmail('alice_nima@gmail.com');
        $this->assertNotNull($user);
        $this->assertEquals('alice32' , $user->getNick() );
        $this->assertEquals($user->pw_encode('123qwe'), $user->getPwd());

        $this->assertInstanceOf('Jili\\ApiBundle\\Entity\\QQUser', $qqUser);
        $this->assertEquals($user->getId(), $qqUser->getUserId());
        $this->assertEquals('973F697E97A60289C8C455B1D65FF5F0', $qqUser->getOpenId() );

        $qqUser1 = $this->em->getRepository('JiliApiBundle:QQUser')->findOneBy(array('userId'=>$user->getId(), 'openId'=>'973F697E97A60289C8C455B1D65FF5F0' ));
        
        $this->assertNotNull( $qqUser1);
        $this->assertInstanceOf('Jili\\ApiBundle\\Entity\\QQUser', $qqUser1);

    }

}
