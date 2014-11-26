<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\FrontendBundle\DataFixtures\ORM\Controller\GameSeeker\LoadGetChestInfoData;
use Jili\BackendBundle\DataFixtures\ORM\Services\GameSeeker\LoadPointsPoolPublishCodeData;

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
    //    if($tn === 'testGetChestInfoAction' ){
            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $fixture = new LoadGetChestInfoData();
            $loader = new Loader();
            $loader->addFixture($fixture);

            if($tn === 'testGetClickActionNormal') {
                $loader->addFixture(new LoadPointsPoolPublishCodeData());
            }
            $executor->purge();
            $executor->execute($loader->getFixtures());

            $this->has_fixture = true;
      //  }
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

        // not signin
        $crawler = $client->request('POST', $url, array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals('{}',$client->getResponse()->getContent(),'not sign in');

        $uid = LoadGetChestInfoData::$USERS[0]->getId() ;
        $session = $client->getContainer()->get('session');
        $session->set('uid', $uid);
        $session->save();
        
        // normal
        $uid = LoadGetChestInfoData::$USERS[0]->getId() ;
        $session = $client->getContainer()->get('session');
        $session->set('uid', $uid);
        $session->save();

        $path_configs= $container->getParameter('game_seeker_config_path');
        @unlink($path_configs['chest']);
        $crawler = $client->request('POST',$url,array(),array(),array('HTTP_X-Requested-With'=>'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $gameSeekerDaily = $this->em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=> $uid));
        $token = $gameSeekerDaily->getToken();

        echo $token,PHP_EOL;
        $this->assertNotNull($gameSeekerDaily);
        $expected = '{"code":0,"data":{"countOfChest":5,"token":"'.$token.'"}}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'normal');
        // to let the token seed changed
        sleep(2); 

        // again
        @unlink($path_configs['chest']);
        $crawler = $client->request('POST',$url,array(),array(),array('HTTP_X-Requested-With'=>'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );

        $gameSeekerDailyList = $container->get('doctrine.orm.entity_manager')->getRepository('JiliFrontendBundle:GameSeekerDaily')->findBy(array('userId'=> $uid));
        $this->assertNotEmpty($gameSeekerDailyList);

        $token_again = $gameSeekerDailyList[0]->getToken();
        echo $token_again,PHP_EOL;

        $this->assertNotEquals($token, $token_again);

        $expected = '{"code":0,"data":{"countOfChest":5,"token":"'.$token_again.'"}}';
        $this->assertEquals($expected, $client->getResponse()->getContent(),'normal');

        // another user , opened again, but not clicked yet
        $user = LoadGetChestInfoData::$USERS[1];
        $session->set('uid', $user->getId());
        $session->save();
        @unlink($path_configs['chest']);
        $crawler = $client->request('POST', $url, array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals( '{"code":1}',$client->getResponse()->getContent()  );

        //completed user , clicked got points > 0 ;
        $user = LoadGetChestInfoData::$USERS[2];
        $session->set('uid', $user->getId());
        $session->save();
        @unlink($path_configs['chest']);
        $crawler = $client->request('POST', $url, array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals( '{"code":1}',$client->getResponse()->getContent()  );

        //completed user , clicked got points == 0 ;
        $user = LoadGetChestInfoData::$USERS[3];
        $session->set('uid', $user->getId());
        $session->save();
        $crawler = $client->request('POST', $url, array(), array(), array('HTTP_X-Requested-With'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode() );
        $this->assertEquals( '{"code":1}',$client->getResponse()->getContent()  );

    }


    /**
     * with no points strategy configed.
     * @group issue_524
     * @group debug 
     */
    function testGetClickActionNoPointsStrategy() 
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_gameseeker_click');
        $this->assertEquals('/game-seeker/click', $url, 'router');
        
        // with no points strategy
        $token = LoadGetChestInfoData::$GAMESEEKLOGS[0]->getToken();

        $user = LoadGetChestInfoData::$USERS[1];
        $session = $container->get('session');
        $session->set('uid', $user->getId());
        $session->save();
         
        $path_configs= $container->getParameter('game_seeker_config_path');
        @unlink($path_configs['points_strategy']);
        @unlink($path_configs['points_pool']);

        $crawler = $client->request('POST', $url, array('token'=>$token), array(),array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
//        $this->assertEquals('{"code":0,"message":"\u5bfb\u5b9d\u7bb1\u6210\u529f","data":{"points":-1}}', $client->getResponse()->getContent());
        $this->assertEquals('{"code":0,"message":"\u5bfb\u5230\u4e00\u4e2a\u7a7a\u5b9d\u7bb1","data":{"points":0}}', $client->getResponse()->getContent());

        $this->assertFalse($session->has('points'));
//        $this->assertEquals(87, $session->get('points'));
    }

    /**
     * @group issue_524
     * @group debug 
     */
    function testGetClickActionNormal() 
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_gameseeker_click');
        // normal
        $token = LoadGetChestInfoData::$GAMESEEKLOGS[0]->getToken();
        $user = LoadGetChestInfoData::$USERS[1];
        $session = $container->get('session');
        $session->set('uid', $user->getId());
        $session->save();
        $path_configs= $container->getParameter('game_seeker_config_path');
        @unlink($path_configs['points_strategy']);
        @unlink($path_configs['points_pool']);

        $crawler = $client->request('POST', $url, array('token'=>$token), array(),array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($session->has('points'));
        $this->assertGreaterThan($user->getPoints(), $session->get('points'));

        $diff = $session->get('points') - $user->getPoints();
        $this->assertEquals('{"code":0,"message":"\u5bfb\u5b9d\u7bb1\u6210\u529f","data":{"points":'.$diff.'}}', $client->getResponse()->getContent());
        
    }
}
