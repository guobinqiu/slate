<?php

namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class ActivityGatheringControllerTest extends WebTestCase
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
       // $tn  = $this->getName();
       // if (in_array($tn ,array('testAddOrderNormal', 'testCheckinNormal') )){
       //     $fixture = new LoadUserData();
       //     $fixture->setContainer($container);
       //     $loader = new Loader();
       //     $loader->addFixture($fixture);
       //     $executor->execute($loader->getFixtures());
       // }
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
    public function testIndexNormal()
    {
        $client = $this->client; 
        $container = $this->container;
        $url =$container->get('router')->generate('jili_backend_activitygathering_getordertotal'); 
        $this->assertEquals('https://localhost/admin/activity/gathering/order-total/get', $url);
        $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }


}
