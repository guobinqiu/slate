<?php
namespace Jili\ApiBundle\Tests\Services;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class UserLoginTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $cotainer;

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
        $this->container = $container;

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $tn = $this->getName();
        if( $tn=='testDoLogin' ) {

            // load fixtures
            $fixture = new UserLoginTestFixture();

            $loader = new Loader();
            $loader->addFixture($fixture);

            $executor->execute($loader->getFixtures());

        }

    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testContainerGet()
    {
        $container = $this->container;
        $login_service = $container->get('login.listener');
        $this->assertInstanceOf('Jili\\ApiBundle\\Services\\UserLogin',$login_service, 'login listener is instance of  Jili\\ApiBundle\\Services\\UserLogin');
    }

    public function testDoLogin() 
    {
        $container = $this->container;
        $result = $container->get('login.listener')
            ->doLogin(array(
                'email'=> 'alice.nima@voyagegroup.com.cn',
                'pwd'=>'111111' ,
                'method'=> 'POST',
                'client_ip'=> '127.0.0.1'
            ));
        print "What1[result=$result]";
        $this->assertEquals('ok', $result,  '"ok" for alice login successuflly');

        $result = $container->get('login.listener')
            ->doLogin(array(
                'email'=> 'bob.inch@voyagegroup.com.cn',
                'pwd'=>'111111' ,
                'method'=> 'POST',
                'client_ip'=> '127.0.0.1'
            ));

            print "What2";
        $this->assertEquals('ok', $result,  '"ok" for bob login successuflly');

        $user  = UserLoginTestFixture::$USERS[1];
        $em = $this->em;

        $user_updated = $em->getRepository('JiliApiBundle:User')->findOneBy(array('id'=>$user->getId()));

        $user_stm =   $em->getConnection()->prepare('select * from user where id =  '.$user->getId());
        $user_stm->execute();
        $user_updated =$user_stm->fetchAll();

        $this->assertNotEmpty($user_updated[0]['pwd'], 'password should not be empty');
        $this->assertEquals( 2, $user_updated[0]['password_choice'], 'after migrate password , password_choice should be 2');

    }
}


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\UserWenwenLogin;


class UserLoginTestFixture extends AbstractFixture implements FixtureInterface 
{

   
    public static $USERS;
    public static $USER_LOGIN;

    public function __construct() 
    {
        self::$USERS = array();
        self::$USER_LOGIN = array();

    }


    /**
    * {@inheritDoc}
    */
    public function load(ObjectManager $manager) 
    {
        //load data for testing .
        $user = new User();
        $user->setNick('alic32');
        $user->setEmail('alice.nima@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED);

        $manager->persist($user);
        $manager->flush();
        self::$USERS[] = $user;

        //load data for testing .
        $user = new User();
        $user->setNick('bob32');
        $user->setEmail('bob.inch@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setRewardMultiple(1);
        $user->setPwd('111111');
        $user->setOriginFlag(User::ORIGIN_FLAG_WENWEN);
        $user->setPasswordChoice(User::PWD_WENWEN);
        $user->setIsEmailConfirmed(User::EMAIL_CONFIRMED);

        $manager->persist($user);
        $manager->flush();

        self::$USERS[] = $user;
        $login = new UserWenwenLogin();
        $login->setUser($user)
            ->setLoginPassword('aPaR9Ucsu4U=') // 123123 dZcCU45B0rk=
            ->setLoginPasswordCryptType('blowfish')
            ->setLoginPasswordSalt('★★★★★アジア事業戦略室★★★★★');
        $manager->persist($login);
        $manager->flush();
        self::$USER_LOGIN[] =  $login;
    }


}




