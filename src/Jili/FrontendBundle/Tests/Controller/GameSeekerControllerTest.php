<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Jili\FrontendBundle\DataFixtures\ORM\LoadGameSeekerGetChestInfoData;

class GameSeekerControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     **/
    private $em;

    private $has_fixture;

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

        // $container =  static::$kernel->getContainer();
        $tn = $this->getName();
        if($tn === '' ){

        }
        $this->has_fixture = false ;
        $this->client = static::createClient();
        $this->em  = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        if ($this->has_fixture) {
            $this->em->close();
        }
    }

    /**
     * @group issue_524
     * @group debug 
     */
    function testGetChestInfoAction() 
    {
        $client = $this->client;
        $container = $client->getContainer();

        $url =$container->get('router')->generate('jili_frontend_gameseeker_getchestinfo');
        $this->assertEquals('/game-seeker/getChestInfo',$url,'router '  );


        // not a post method ,
        $crawler = $client->request('GET', $url);
        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        $crawler = $client->request('POST', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        var_dump($client->getResponse()->getContent());
//        $client->request('DELETE', $url , $form_data, array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));


    }

    /**
     * @group issue_524
     * @group debug 
     */
    function testGetClickAction() 
    {
        $client = $this->client;
        $container = $client->getContainer();

        $url =$container->get('router')->generate('jili_frontend_gameseeker_click');
        $this->assertEquals('/game-seeker/click',$url,'router '  );

    }
}
