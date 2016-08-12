<?php

namespace Jili\ApiBundle\Tests\Controller;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserInfoCodeData;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jili\ApiBundle\Controller\UserController;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserSetPasswordCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserResetPasswordCodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserReSendCodeData;
use JMS\JobQueueBundle\Entity\Job;

class UserControllerTest extends WebTestCase
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
        $container  = static::$kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $loader = new Loader();
        $tn = $this->getName();
        if ($tn == 'testResetPasswordAction') {
            $fixture = new LoadUserResetPasswordCodeData();
            $fixture->setContainer($container);
            $loader->addFixture($fixture);
        } else if ($tn == 'testReSend') {
            $fixture = new LoadUserReSendCodeData();
            $fixture->setContainer($container);
            $loader->addFixture($fixture);
        } else {
            $fixture = new LoadUserInfoCodeData();
            $fixture->setContainer($container);
            $loader->addFixture($fixture);
        }
        $executor->execute($loader->getFixtures());

        $this->container = $container;
        $this->em  = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
#        header_remove();
        parent::tearDown();
       $this->em->close();
    }

    /**
     * @group user
     * @group login
     */
    public function testLogoutWithTokenAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $router = $container->get('router');
        $logger= $container->get('logger');
        // login
        $url = $container->get('router')->generate('_user_login', array(), true);
        //echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $query = array('email'=> 'alice.nima@gmail.com');
        $em = $this->em;
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }
        $uid = $user->getId();
        unset($user);
        // clean token
        $em->getRepository('JiliApiBundle:User')->cleanToken($uid);
        $user = $em->getRepository('JiliApiBundle:User')->find($uid);//$query['email']);

        $this->assertEmpty($user->getToken());
        unset($user);

        //logout
        $url_logout = $router->generate('_user_logout' , array(), true);
        //echo $url_logout,PHP_EOL;
        $crawler = $client->request('GET', $url_logout ) ;

        $user = $em->getRepository('JiliApiBundle:User')->find($uid);
        $this->assertEmpty($user->getToken());
        unset($user);
    }
    /**
     * @group user
     * @group login
     */
    public function testLogoutAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $router = $container->get('router');
        $logger= $container->get('logger');
        // logout
        $url_logout = $router->generate('_user_logout' , array(), true);
        //echo $url_logout,PHP_EOL;
        $crawler = $client->request('GET', $url_logout ) ;

        $em = $this->em;
        $query = array('email'=> 'alice.nima@gmail.com');
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($query['email']);
        if(! $user) {
            echo 'bad email:',$query['email'], PHP_EOL;
            return false;
        }

        $this->assertEquals(302, $client->getResponse()->getStatusCode() );
        $session = $container->get('session');
        $this->assertFalse( $session->has('uid'));
        $this->assertFalse( $session->has('nick'));

        $cookies  = $client->getCookieJar() ;
        $this->assertEmpty(  $cookies->get('jili_uid' ,'/'));
        $this->assertEmpty(  $cookies->get('jili_nick' ,'/'));
        $this->assertEmpty(  $cookies->get('jili_rememberme' ,'/'));



    }

    /**
     * @group user
     * @group dev-merge-ui-reset-password
     */
    public function testResetPasswordAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $logger= $container->get('logger');

        // reset email
        $user = LoadUserResetPasswordCodeData::$ROWS[0];

        $query = array('email'=> $user->getEmail());
        $url = $container->get('router')->generate('_user_reset', $query ) ;
        $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        //$this->assertEquals('1', $client->getResponse()->getContent());

        $setPasswordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneByUserId($user->getId());
        $url = $container->get('router')->generate('_user_resetPass',array('code'=>$setPasswordCode->getCode(),'id'=>$user->getId() ),true);
        $client->request('GET', $url ) ;
        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() , 'GET forget pass url status check' );

        $form = $crawler->filter('form[id=form1]')->form();
        $form['pwd'] = 1;
        $form['pwdRepeat'] = 1;
        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertContains('用户密码为5-100个字符，密码至少包含1位字母和1位数字', $client->getResponse()->getContent(), 'password error');

        $form = $crawler->filter('form[id=form1]')->form();
        $form['pwd'] = '111111q';
        $form['pwdRepeat'] = '111111q';
        // submit the form
        $crawler = $client->submit($form);

        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertContains('密码修改成功', $client->getResponse()->getContent(), 'password error');

        //check data
        $user = $em->getRepository('JiliApiBundle:User')->find($user->getId());
        $this->assertEquals(\Jili\ApiBundle\Entity\User::PWD_WENWEN, $user->getPasswordChoice());
        $wenwenLogin = $em->getRepository('JiliApiBundle:UserWenwenLogin')->findOneByUser($user);
        $this->assertNotNull($wenwenLogin);
        $this->assertTrue($wenwenLogin->isPwdCorrect('111111q'));

        //check can login
        $url = $container->get('router')->generate('_login', array (), true);
        $client->request('POST', $url, array (
            'email' => 'alice.nima@gmail.com',
            'pwd' => '111111q',
            'remember_me' => '1'
        ));
        $client->followRedirect();
    }

    /**
     * @group user-password
     * @group issue_381
     */
    public function testPasswordAction()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $logger= $container->get('logger');

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserSetPasswordCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

        $uid = LoadUserSetPasswordCodeData::$USER[0]->getId();
        $code =  LoadUserSetPasswordCodeData::$SET_PASSWORD_CODE[0]->getCode();

        $query = array( 'token'=> $code, 'uid'=> $uid );

        //todo: add https
        $url = $container->get('router')->generate('_user_signup_activate', $query ) ;
        $url_expected = '/user/activate/'. $code. '/'. $uid;
        $this->assertEquals($url_expected, $url);

        $crawler =$client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $form = $crawler->selectButton('创建账号')->form();

        $form['password[first]'] ->setValue( 'qwe123');
        $form['password[second]'] ->setValue( 'qwe123');
        $form['agreement']->tick() ;
        unset($form['but']); //NOTICE, this is an extra field.

        $client->submit($form );
        $this->assertEquals(302, $client->getResponse()->getStatusCode() );

        $crawler = $client->followRedirect();

        //  check the redirected url.
        $url_expected = $container->get('router')->generate('_user_regSuccess') ;
        $this->assertEquals( $url_expected, $client->getRequest()->getRequestUri());

        //  check session messages
        $this->assertEquals('恭喜，密码设置成功！', $crawler->filter('h2')->text());
       
        $user_stm =   $em->getConnection()->prepare('select * from user where id =  '.$uid);
        $user_stm->execute();
        $user_updated =$user_stm->fetchAll();
        $this->assertEquals( 2, $user_updated[0]['password_choice'], 'after migrate password , password_choice should be 2');


    }

    /**
     * @group user_reg
     */
    public function testReSend()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $user = LoadUserReSendCodeData::$ROWS[0];
        $client->request('GET', '/user/reSend', array (
                'id'=>$user->getId(),
                'code'=>'testcode100',
                'nick'=>'',
                'email'=>$user->getEmail()
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('1', $client->getResponse()->getContent());
    }
    
    public function testRegActionExistingEmail() 
    {
        $client = static::createClient(array(), array('HTTP_USER_AGENT'=>'symonfy/2.0' ,'REMOTE_ADDR'=>'121.199.27.128', 'HTTPS' => true) );
        $container = $client->getContainer();

        $em = $this->em;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new \Jili\ApiBundle\DataFixtures\ORM\LoadUserData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());


        $url = $container->get('router')->generate('_user_reg', array(), false);
        $crawler = $client->request('GET', $url  ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() ,'get the register page return 200');
        $session = $container->get('session'); 
        $captcha = $session->get('gcb_captcha');
        $phrase = $captcha ['phrase'] ;

        $email = 'user@voyagegroup.com.cn';
        $form = $crawler->filter('form[name=signup_form]')->form();
        $form['signup[nickname]']->setValue( 'user32' );
        $form['signup[email]']->setValue( $email );
        $form['signup[password][first]'] ->setValue( 'qwe123');
        $form['signup[password][second]'] ->setValue( 'qwe123');
        $form['signup[captcha]']->setValue( $phrase );
        $form['signup[unsubscribe]']->tick() ;
        $form['signup[agreement]']->tick() ;

        $crawler = $client->submit($form );
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email );
        $this->assertNotNull($user, 'user should not be null');

        $this->assertEquals('邮箱"user@voyagegroup.com.cn"是无效的.该邮箱已被使用，请到邮箱查找激活邮件，还有问题？请联系cs@91wenwen.net',
            $crawler->filter('input[id=signup_email]')->siblings()->last()->text(),
            'voyagegroup.com.cn is invalid mail server; user with same email exists');

    }

    /**
     * @group debug
     */
    public function testRegAction() 
    {
        $em=$this->em;
        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();


        $client = static::createClient(array(), array('HTTP_USER_AGENT'=>'symonfy/2.0' ,'REMOTE_ADDR'=>'121.199.27.128', 'HTTPS' => true));
        $container = $client->getContainer();
        $router = $container->get('router');
        $em = $this->em;
        $url = $container->get('router')->generate('_user_reg', array(), false);

        $crawler = $client->request('GET', $url  ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode() ,'get the register page return 200');
        $session = $container->get('session'); 
        $captcha = $session->get('gcb_captcha');
        $phrase = $captcha ['phrase'] ;

        $email = 'alice.nima@gmail.com';
        $form = $crawler->filter('form[name=signup_form]')->form();
        $form['signup[nickname]']->setValue( 'alice32' );
        $form['signup[email]']->setValue( $email );
        $form['signup[password][first]'] ->setValue( 'qwe123');
        $form['signup[password][second]'] ->setValue( 'qwe123');
        $form['signup[captcha]']->setValue( $phrase );
        $form['signup[agreement]']->tick() ;
        $form['signup[unsubscribe]']->untick() ;

        $crawler = $client->submit($form );

        $this->assertEquals(302, $client->getResponse()->getStatusCode() );
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email );

        $this->assertNotNull($user, 'user should not be null');
        $this->assertEquals('symonfy/2.0',$user->getCreatedUserAgent(), 'user_agent should be symfony/2.0');
        $this->assertEquals('121.199.27.128',$user->getCreatedRemoteAddr(), 'client ip when reg should be 121.199.27.128');

        // check passsword token

        $setPasswordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneBy(array('userId'=>$user->getId()));
         $this->assertNotNull($setPasswordCode, 'check the set_password_code for the created user');
         $this->assertNotEmpty($setPasswordCode->getCode(), 'check the set_password_code.code not empty for the created user');

        //check email job inserted
        $jobs =  $em->getRepository('JMSJobQueueBundle:Job')->findAll();
        $this->assertCount(1, $jobs, 'only 1 job ' );
        $job=$jobs[0];
        $this->assertEquals(Job::STATE_PENDING,$job->getState() ,'pending');
        //$this->assertEquals('webpower-mailer:signup-confirm',$job->getCommand() ,'the comand ');
        $this->assertEquals('91wenwen_signup',$job->getQueue() ,'the queue');


//        $args = array( '--campaign_id=1','--group_id=81','--mailing_id=9','--email=alice.nima@gmail.com',
//            '--title=先生/女士',
//            '--name=alice32',
//            '--register_key='.$setPasswordCode->getCode() );

        //$this->assertEquals($args ,$job->getArgs() ,'pending');



        $userEdmUnsubscriber = $em->getRepository('JiliApiBundle:UserEdmUnsubscribe')->findOneBy(array('userId'=>$user->getId()));

        $this->assertNotNull($userEdmUnsubscriber, 'unsubscribe edm');

    }
}
