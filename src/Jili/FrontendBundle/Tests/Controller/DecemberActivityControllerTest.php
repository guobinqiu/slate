<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class DecemberActivityControllerTest extends WebTestCase
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
//        $tn = $this->getName();


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
     * @group issue_537
     */
    public function testIndexAction()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_index');
        $this->assertEquals('/activity/december/', $url);
    }

    /**
     * @group issue_537
     * @group debug 
     */
    public function testAddTaobaoOrderActionValidation()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_addtaobaoorder');
        $this->assertEquals('/activity/december/add-taobao-order', $url);

        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        // check uri 
        $session = $client->getRequest()->getSession();
        $this->assertTrue( $session->has('goToUrl'));
        $this->assertEquals('/activity/december/', $session->get('goToUrl'));

//        $form = $crawler->selectButton('submit')->getForm();

        // $client->getResponse()->getContent();

// invalid form inputs 
// duplicated post ? 
        $session  = $client->getRequest()->getSession();
        $session->set('uid' , 1);
        $session->save();

    }
    /**
     * @group issue_537
     * @group debug 
     */
    public function testAddTaobaoOrderActionNormal()
    {
        $client = static::createClient();
        $container  = static::$kernel->getContainer();
        $url =$container->get('router')->generate('jili_frontend_decemberactivity_addtaobaoorder');
        $this->assertEquals('/activity/december/add-taobao-order', $url);

        $session  = $container->get('session');
        $session->set('uid' , 1);
        $session->save();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

//        echo $client->getResponse()->getContent();

        $form = $crawler->selectButton('submit')->getForm();
        $form['order[orderId]']->setValue('myorderid001');
        $form['order[orderPaid]']->setValue(5.0);
        $crawler = $client->submit($form);

        // check result
        $expected_record= $this->em 
            ->getRepository('JiliFrontendBundle:GameEggsBreakerTaobaoOrder')
            ->findOneBy(array('userId'=> 1, 'orderId'=>'myorderid001' , 'orderPaid'=> 5.0));
        $this->assertNotNull( $expected_record);

    }
}
