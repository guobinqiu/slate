<?php

namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameSeekerControllerTest extends WebTestCase
{
    
    /**
     * @group issue_524
     * @group debug 
     */
    function testBuildAction() 
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();

        $url =$container->get('router')->generate('jili_backend_gameseeker_buildpointstrategy');
        $this->assertEquals('/admin/game-seeker/build-points-strategy', $url);

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

    }
}
