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
        
        $form = $crawler->filter('form[name=form1]')->form();

        $form['signup[email]'] ->setValue( 'alice_nima@gmail.com');
        $form['signup[nickname]'] ->setValue( 'alice32');
        $form['signup[captcha]']->setValue($phrase) ;

        $client->submit($form );


        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        // Check that an e-mail was sent
        $this->assertEquals(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertEquals(302, $client->getResponse()->getStatusCode() );

        $crawler = $client->followRedirect();
        

        // post invalid data
    }

}
