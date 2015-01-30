<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;


class LandingControllerTest extends WebTestCase
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

        // purge tables;
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
     * @group issue_448 
     */
    public function testExternal()
    {
        $em = $this->em;
        $client = static::createClient();
        $client->enableProfiler();

        $container = $client->getContainer();

        $url = $container->get('router')->generate('_landing_external' );
        $url_expected  ='https://localhost/external-landing';
        $this->assertEquals($url_expected, $url, 'check the routing url, with https://');

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'simple GET Request'  );

        // post valid data
        $session = $container->get('session'); 
        $captcha = $session->get('gcb_captcha');
        $phrase = $captcha ['phrase'] ;
        
        $data=  array('email'=> 'alice_nima@gmail.com', 'nick'=>'alice32' );
        $form = $crawler->filter('form[name=form1]')->form();
        $form['signup[email]'] ->setValue($data['email']);
        $form['signup[nickname]'] ->setValue( $data['nick']);
        $form['signup[captcha]']->setValue($phrase) ;


        $client->submit($form );

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        // Check that an e-mail was sent
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        $user = $em->getRepository('JiliApiBundle:User')->findOneBy( $data);
        $this->assertNotNull($user, 'check the created user');
        $this->assertNull($user->getPwd(), 'check pwd of the created user');

        $setPasswordCode = $em->getRepository('JiliApiBundle:SetPasswordCode')->findOneBy(array('userId'=>$user->getId()));
        $this->assertNotNull($setPasswordCode, 'check the set_password_code for the created user');
        $this->assertNotEmpty($setPasswordCode->getCode(), 'check the set_password_code.code not empty for the created user');
        $url = $container->get('router')->generate('_user_forgetPass',array('code'=>$setPasswordCode->getCode(), 'id'=>$user->getId()),true);
        $body_expected = '<html>' .
            ' <head></head>' .
            ' <body>' .
            '亲爱的'.$data['nick'].'<br/>'.
            '<br/>'.
            '  感谢您注册成为“积粒网”会员！请点击<a href='.$url.' target="_blank">这里</a>，立即激活您的帐户！<br/><br/><br/>' .
            '  注：激活邮件有效期是14天，如果过期后不能激活，请到网站首页重新注册激活。<br/><br/>' .
            '  ++++++++++++++++++++++++++++++++++<br/>' .
            '  积粒网，轻松积米粒，快乐换奖励！<br/>赚米粒，攒米粒，花米粒，一站搞定！' .
            ' </body>' .
            '</html>';
        // Asserting e-mail data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('积粒网-注册激活邮件', $message->getSubject());
        $this->assertEquals('account@91jili.com', key($message->getFrom()));
        $this->assertEquals($user->getEmail(), key($message->getTo()));
        $this->assertEquals($body_expected,$message->getBody());

        $this->assertEquals(302, $client->getResponse()->getStatusCode() );
        $crawler = $client->followRedirect();

        // post invalid data

    }

    /**
     * externalAction with not exists: wenwen code exists email
     * @group issue_448
     */
    public function testExternalActionWithSignUpTrace()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $logger= $container->get('logger');

        // add session
        $session = $container->get('session');
        $session->set('id', '1234567890');
        $session->save();


        $time =time();
        $spm = 'baidu_partnera';

        $this->assertEmpty( $session->get('source_route'));

        // build query with add spm without token;
        $url = $container->get('router')->generate('_landing_external', array('spm'=>$spm) , false);
        $this->assertEquals('https://localhost/external-landing?spm=baidu_partnera', $url);

        // follow to the redirect
        $crawler = $client->request('GET', $url );
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'visit landing page with spm ');

        $session= $container->get('session');
        $this->assertEquals($spm, $session->get('source_route'));

        $session = $container->get('session'); 
        $captcha = $session->get('gcb_captcha');
        $phrase = $captcha ['phrase'] ;
        // post reg form

        $email = 'alice.nima@gmail.com';

        $form = $crawler->filter('form[name=form1]')->form();
        $form['signup[email]'] ->setValue( $email );
        $form['signup[nickname]'] ->setValue( 'alice32');
        $form['signup[captcha]'] ->setValue( $phrase );

        $client->submit($form );
        $this->assertEquals(302, $client->getResponse()->getStatusCode() );

        $crawler = $client->followRedirect();

        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email );

        //  check the redirected url.
        $url_expected = $container->get('router')->generate('_user_checkReg', array('id' => $user->getId() ) ) ;
        $this->assertEquals( $url_expected, $client->getRequest()->getRequestUri());

        // checkings after register.
        $records = $em->getRepository('JiliApiBundle:UserSignUpRoute')->findBy(
            array('userId'=> $user->getId()),
            array('createdAt'=>'desc')
        );

        $this->assertCount( 1, $records, 'check the user_source_logger table');

        // check log file
        $log_path = $container->getParameter('kernel.logs_dir');
        $log_path .= '/../logs_data/'.$container->getParameter('kernel.environment');
        $log_path .= '.user_source.log';

        $this->assertFileExists($log_path, 'check log file exits');

        // fetch the last line of the file.
        $fp = fopen($log_path, 'r');
        fseek($fp, -2, SEEK_END);
        $pos = ftell($fp);
        fseek($fp, $pos--);

        $last_row ='';
        // Loop backword util "\n" is found.
        while((($c = fgetc($fp)) != "\n") && ($pos > 0)) {
            $last_row= $c.$last_row;
            fseek($fp, $pos--);
        }
        fclose($fp);

        $arr = explode("\t", $last_row);
        $this->assertCount(5,$arr, 'check the content of log file');
        $this->assertEquals( 'user_source',$arr[2], 'check the content of log file');
        $this->assertEquals( $session->get('source_route'), $arr[4], 'check the content of log file');
    }


    /**
     * @group issue_448
     */
    public function testHomepageWithSignUpTrace()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $logger= $container->get('logger');

        $session = $container->get('session');
        $time =time();
        $spm = 'baidu_partnerx';

        $this->assertEmpty( $session->get('source_route'));

        // build query with add spm without token;
        $url = $container->get('router')->generate('_homepage', array('spm'=>$spm) , false);
        $this->assertEquals('/?spm=baidu_partnerx', $url);

        // follow to the redirect
        $crawler = $client->request('GET', $url );
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'visit homepage with spm as landing page');

        $session= $container->get('session');
        $this->assertEquals($spm, $session->get('source_route'));

        $session = $container->get('session'); 
        $captcha = $session->get('gcb_captcha');
        $phrase = $captcha ['phrase'] ;

        $url = $container->get('router')->generate('_user_reg' );
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // post reg form

        $email = 'alice.nima@gmail.com';
        $form = $crawler->filter('form[name=form1]')->form();
        $form['email'] ->setValue( $email );
        $form['nick'] ->setValue( 'alice32');
        $form['captcha'] ->setValue( $phrase );

        $client->submit($form );
        $this->assertEquals(302, $client->getResponse()->getStatusCode() );

        $crawler = $client->followRedirect();

        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email );

        //  check the redirected url.
        $url_expected = $container->get('router')->generate('_user_checkReg', array('id' => $user->getId() ) ) ;

        $this->assertEquals( $url_expected, $client->getRequest()->getRequestUri());

        // checkings after register.
        $records = $em->getRepository('JiliApiBundle:UserSignUpRoute')->findBy(
            array('userId'=> $user->getId()),
            array('createdAt'=>'desc')
        );

        $this->assertCount( 1, $records, 'check the user_source_logger table');

        // check log file
        $log_path = $container->getParameter('kernel.logs_dir');
        $log_path .= '/../logs_data/'.$container->getParameter('kernel.environment');
        $log_path .= '.user_source.log';

        $this->assertFileExists($log_path, 'check log file exits');

        // fetch the last line of the file.
        $fp = fopen($log_path, 'r');
        fseek($fp, -2, SEEK_END);
        $pos = ftell($fp);
        fseek($fp, $pos--);

        $last_row ='';
        // Loop backword util "\n" is found.
        while((($c = fgetc($fp)) != "\n") && ($pos > 0)) {
            $last_row= $c.$last_row;
            fseek($fp, $pos--);
        }
        fclose($fp);

        $arr = explode("\t", $last_row);
        $this->assertCount(5,$arr, 'check the content of log file');
        $this->assertEquals( 'user_source',$arr[2], 'check the content of log file');
        $this->assertEquals( $session->get('source_route'), $arr[4], 'check the content of log file');
    }
}
