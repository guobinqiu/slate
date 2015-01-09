<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class MonthActivityControllerTest extends WebTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function setUp()
    {
        static::$kernel = static::createKernel( array('environment'=>'test') );
        static::$kernel->boot();

        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $container  = static::$kernel->getContainer();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $tn  = $this->getName();
        if (in_array($tn ,array('testAddOrderNormal', 'testCheckinNormal') )){
            $fixture = new LoadUserData();
            $fixture->setContainer($container);
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
        }
        $this->container = $container;
        $this->em  = $em;
        $this->client = static::CreateClient();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        $this->em->close();
        parent::tearDown();
    }

    /**
     * checkin form post
     * @group issue_618
     */
    public function testCheckinNormal()
    {
        $client = $this->client; 
        $em =$this->em;
        $container = $this->container;
        $url =$container->get('router')->generate('jili_api_monthactivity_gatheringcheckin'); ;
        //set session
        $this->assertEquals('/monthActivity/activity/gathering/checkin', $url);


        $user = LoadUserData::$USERS[0];
        $session = $container->get('session');
        $session->set('uid', $user->getId());
        $session->save();

        $crawler =  $client->request('GET', $url );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // get form , fill form submit 
        // check the result
        $session = $client->getRequest()->getSession();
        $session->set('uid', $user->getId());
        $session->save();

        $form = $crawler->selectButton('我要参加')->form();
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
        $entities = $em->getRepository('JiliApiBundle:ActivityGatheringCheckinLog')->findAll();
        $this->assertCount(1, $entities);
        $this->assertEquals($user->getId(), $entities[0]->getUser()->getId());

    }
    // duplicated checkin
    // not session checkin 
    // order form post

    /**
     * checkin form post
     * @group issue_618
     */
    public function testAddOrderNormal()
    {
        $client = $this->client; 
        $em =$this->em;
        $container = $this->container;
        $url =$container->get('router')->generate('jili_api_monthactivity_gatheringaddtaobaoorder'); ;
        //set session
        $this->assertEquals('/monthActivity/activity/gathering/order-add', $url);

        $user = LoadUserData::$USERS[0];
        $session = $container->get('session');
        $session->set('uid', $user->getId());
        $session->save();

        $crawler =  $client->request('GET', $url );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $session = $client->getRequest()->getSession();
        $session->set('uid', $user->getId());
        $session->save();

        $form = $crawler->selectButton('提交')->form();
        $form['activityGatheringOrder[orderIdentity]']= '123451234512345';
        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());


        $actual = $em->getRepository('JiliApiBundle:ActivityGatheringTaobaoOrder')
            ->findOneBy(array('orderIdentity'=>'123451234512345'));
        
        $this->assertNotNull($actual);
        $this->assertInstanceOf('Jili\ApiBundle\Entity\ActivityGatheringTaobaoOrder',$actual);
        $this->assertEquals($user->getId(), $actual->getUser()->getId());

    }

}


