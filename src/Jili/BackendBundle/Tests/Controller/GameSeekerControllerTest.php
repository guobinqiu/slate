<?php

namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameSeekerControllerTest extends WebTestCase
{

    private $em ;
    private $has_fixture;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->container  = static::$kernel->getContainer();
// $em = ?
        $tn = $this->getName();
        if('testPublishAction'=== $tn ){

            $this->has_fixture = true;

        }
    }
    
    /**
     * @group issue_524
     */
    function testBuildAction() 
    {
        $client = $this->client;
        $container = $this->container;

        $url =$container->get('router')->generate('jili_backend_gameseeker_buildpointstrategy');
        $this->assertEquals('https://localhost/admin/game-seeker/build-points-strategy', $url);

        $crawler = $client->request('GET', $url ); 
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('Create')->form();

        $form['form[rules]'] =<<<EOD
1:500
2:100
4:50
10:20
40:5
100:2
500:0
EOD;
        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        //  check the redirected url.
        $this->assertEquals( '/admin/game-seeker/publish-points-strategy', $client->getRequest()->getRequestUri());

    }

    /**
     * @group issue_524
     */
    function testEnableAction() 
    {
        $client = $this->client;
        $container = $this->container;
    }

    /**
     * @group issue_524
     */
    function testPublishAction() 
    {
        $client = $this->client;
        $container = $this->container;
        $url =$container->get('router')->generate('jili_backend_gameseeker_publishpointsstrategy');
        $this->assertEquals( 'https://localhost/admin/game-seeker/publish-points-strategy', $url);
        // form post !
        $crawler = $client->request('GET', $url);

        // $form->
        //$this->assertEquals(200, $client->getResponse()->getStatusCode() );
    }
}
