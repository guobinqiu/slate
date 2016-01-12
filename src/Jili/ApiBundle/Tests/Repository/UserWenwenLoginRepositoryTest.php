<?php

namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class UserWenwenLoginTest extends KernelTestCase
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
        $container  = static::$kernel->getContainer();

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());
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

   public function testCreateOne()
   {
       $user = LoadUserData::$USERS[0];

       $params = array(
           'user_id'=>$user->getId(),
           'salt'=>'abc',
           'crypt_type'=>'md5',
           'password'=>'123456'
       );
       $em = $this->em;
       $login_password  = $em->getRepository('JiliApiBundle:UserWenwenLogin')
           ->createOne($params);

        $passwords_stm  =  $em->getConnection()->prepare('select * from user_wenwen_login');
        $passwords_stm->execute();
        $passwords = $passwords_stm->fetchAll();

        $this->assertNotNull( $passwords);
        $this->assertCount(1,  $passwords);
        $this->assertEquals(1,  $password[0]['user_id']);
        $this->assertEquals('salt',  $passwords[0]['login_password_crypt_type']);
        $this->assertEquals('md5',  $passwords[0]['login_password_salt']);
        $this->assertEquals('123456',  $passwords[0]['login_password']);
   }

}
