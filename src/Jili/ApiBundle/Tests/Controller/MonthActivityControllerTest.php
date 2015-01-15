<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
use Symfony\Component\Filesystem\Filesystem;

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
        $this->assertEquals('/monthActivity/gathering/order-add', $url);

        $user = LoadUserData::$USERS[0];
        $session = $container->get('session');
        $session->set('uid', $user->getId());
        $session->save();

        $crawler =  $client->request('GET', $url );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $session = $client->getRequest()->getSession();
        $session->set('uid', $user->getId());
        $session->save();

        $form = $crawler->selectButton('')->form();
        $form['activityGatheringOrder[orderIdentity]']= '123451234512345';
        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());


        $actual = $em->getRepository('JiliApiBundle:ActivityGatheringTaobaoOrder')
            ->findOneBy(array('orderIdentity'=>'123451234512345'));
        
        $this->assertNotNull($actual);
        $this->assertInstanceOf('Jili\ApiBundle\Entity\ActivityGatheringTaobaoOrder',$actual);
        $this->assertEquals($user->getId(), $actual->getUser()->getId());

    }

    /**
     * @group issue_618
     */
    public function testOrderCount()
    {

        $client = $this->client; 
        $em =$this->em;
        $container = $this->container;
        $url =$container->get('router')->generate('jili_api_monthactivity_gatheringtaobaoordercount'); 
        //not ajax request
        $this->assertEquals('/monthActivity/gathering/order-count', $url);
        $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{}', $client->getResponse()->getContent());
        // no fixtures
        
        $config =$container->getParameter('activity_gathering');
        $file = $config['taobao_order_total_src'];
        echo $file; 
        @unlink($file);
        $client->request('GET', $url, array(), array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"code":0,"data":{"total":0}}', $client->getResponse()->getContent());

        $fs = new Filesystem();
        $fs->mkdir(dirname($file));
        // with fixture  fixtures
        file_put_contents( $file,json_encode( array('total'=> 23)));

        $client->request('GET', $url, array(), array(), array('HTTP_X-Requested-with'=> 'XMLHttpRequest'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"code":0,"data":{"total":23}}', $client->getResponse()->getContent());

        $fs->remove($file);
    }

}


