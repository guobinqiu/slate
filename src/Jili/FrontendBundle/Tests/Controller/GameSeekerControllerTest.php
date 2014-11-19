<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\FrontendBundle\DataFixtures\ORM\Controller\GameSeeker\LoadGetChestInfoData;

class GameSeekerControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     **/
    private $em;

    /**
     * @var boolean 
     **/
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
        $this->has_fixture = false ;
        $tn = $this->getName();
        if($tn === 'testGetChestInfoAction' ){
            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $fixture = new LoadGetChestInfoData();
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->purge();
            $executor->execute($loader->getFixtures());

            $this->has_fixture = true;
        }
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
     */
    function testGetChestInfoAction() 
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();

        $url =$container->get('router')->generate('jili_frontend_gameseeker_getchestinfo');
        $this->assertEquals('/game-seeker/getChestInfo',$url,'router '  );

        // not a post method ,
        $crawler = $client->request('GET', $url);
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'not POST request');

        //not ajax requet
        $crawler = $client->request('POST', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('{}',$client->getResponse()->getContent(),'not ajax ');

        $crawler = $client->request('POST', $url, array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('{}',$client->getResponse()->getContent(),'not sign in');

        $session = $client->getContainer()->get('session');
        $session->set('uid', 1199);
        $session->save();

        $crawler = $client->request('POST', $url, array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $gameSeekerDaily = $this->em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=> 1199/*, 'clickedDay'=>date('Y-m-d') */) );

        $this->assertNotNull($gameSeekerDaily);
        $expected = '{"code":0,"data":{"countOfChest":3,"token":"'.$gameSeekerDaily->getToken().'"}}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'normal');
    }

    /**
     * @group issue_524
     * @group debug 
     */
    function testGetClickAction() 
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_gameseeker_click');
        $this->assertEquals('/game-seeker/click',$url,'router '  );

    }
}
