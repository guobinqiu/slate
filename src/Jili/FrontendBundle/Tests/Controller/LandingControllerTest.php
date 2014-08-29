<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

// $test_name = $this->getName();
// if( in_array( $test_name, array('test'))){
// }


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
     * @group issue_448 
     */
    public function testExternal()
    {
$em = $this->em;
        $client = static::createClient();
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

        $client->enableProfiler();
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

}
