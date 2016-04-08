<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;


class SingupControllerTest extends WebTestCase
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
        $client = static::createClient();
        $container= static::$kernel->getContainer();
        $em = $container->get('doctrine')->getManager();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new SignupControllerTestFixture();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());


        $this->client= $client;
        $this->container = $container;
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
     * @group debug
     */
    public function testRegisterConfirmActionInvalidCode()
    {
        $client= $this->client;
        $container= $this->container;
        $em = $this->em;

        $url = $container->get('router')->generate('_signup_confirm_register', array('register_key'=>'a') );
        $this->assertEquals('https://localhost/confirmRegister/register_key/a',$url, 'register confirm link');

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'not exists password code'  );

        $this->assertContains('404错误，这个页面被大鲨鱼劫走了~', $client->getResponse()->getContent(),' 404');
    }

    public function testRegisterConfirmAction()
    {
        $client= $this->client;
        $container= $this->container;
        $em = $this->em;

        $user = SignupControllerTestFixture::$USER[0];
        $password_code = SignupControllerTestFixture::$SET_PASSWORD_CODE[0];

        $url = $container->get('router')->generate('_signup_confirm_register', array('register_key'=>$password_code->getCode()) );
        $crawler = $client->request('GET', $url ) ;

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'request with a valide password code'  );
        

        $user_stm =   $em->getConnection()->prepare('select * from user where id =  '.$user->getId());
        $user_stm->execute();
        $user_updated =$user_stm->fetchAll();
        $this->assertEquals(1, $user_updated[0]['is_email_confirmed'], 'is_email_confirmed should be true');
    }

}

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Jili\ApiBundle\Entity\User;
use Jili\ApiBundle\Entity\SetPasswordCode;
class SignupControllerTestFixture  extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface, OrderedFixtureInterface
{
    static public $USER ;
    public static $SET_PASSWORD_CODE ;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$USER = array();
        self::$SET_PASSWORD_CODE = array();
    }
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setNick('chiang32');
        $user->setEmail('chiangtor@gmail.com');
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));
        $user->setPasswordChoice(User::PWD_WENWEN);
        $user->setCampaignCode('offerwow');

        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();

        $setPasswordCode = new SetPasswordCode();
        $setPasswordCode->setUserId($user->getId());

        $str = 'jilifirstregister';
        $code = md5($user->getId().str_shuffle($str));
        $setPasswordCode->setCode($code);
        $setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));

        $manager->persist($setPasswordCode);
        $manager->flush();

        $this->addReference('user0', $user);
        $this->addReference('set_password_code0', $setPasswordCode);
        self::$USER[] = $user;
        self::$SET_PASSWORD_CODE[] = $setPasswordCode;

        ###### //  user 1
        $user = new User();
        $user->setNick('alice32');
        $user->setEmail('alice.nima@gmail.com');
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));

        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();
        ######
        ######         // with invalid create_time
        $setPasswordCode = new SetPasswordCode();
        $setPasswordCode->setUserId($user->getId());

        $str = 'jilifirstregister';
        $code = md5($user->getId().str_shuffle($str));
        $setPasswordCode->setCode($code);
        $invalid_created =new \DateTime();
        $invalid_created ->setTimestamp( time() - SetPasswordCode::$VALIDATION_OF_SIGNUP_ACTIVATE -1  );
        $setPasswordCode->setCreateTime($invalid_created );
        $setPasswordCode->setIsAvailable($this->container->getParameter('init_one'));
        ######
        $manager->persist($setPasswordCode);
        $manager->flush();
        ######
        $this->addReference('user1', $user);
        $this->addReference('set_password_code1', $setPasswordCode);
        self::$USER[] = $user;
        self::$SET_PASSWORD_CODE[] = $setPasswordCode;
        ###### //  user2
        $user = new User();
        $user->setNick('centeRay32');
        $user->setEmail('center_ay@sohu.com');
        $user->setPoints($this->container->getParameter('init'));
        $user->setIsInfoSet($this->container->getParameter('init'));
        $user->setRewardMultiple($this->container->getParameter('init_one'));

        $user->setPwd('123qwe');
        $manager->persist($user);
        $manager->flush();

        // with invalid is_avaiable
        $setPasswordCode = new SetPasswordCode();
        $setPasswordCode->setUserId($user->getId());

        $str = 'jilifirstregister';
        $code = md5($user->getId().str_shuffle($str));
        $setPasswordCode->setCode($code);
        $setPasswordCode->setIsAvailable( 0 );

        $manager->persist($setPasswordCode);
        $manager->flush();

        $this->addReference('user2', $user);
        $this->addReference('set_password_code2', $setPasswordCode);
        self::$USER[] = $user;
        self::$SET_PASSWORD_CODE[] = $setPasswordCode;

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
