<?php
namespace Jili\ApiBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

class QQLoginControllerTest extends WebTestCase
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
        $container  = static::$kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

    //    // load fixtures
    //    $fixture = new LoadApiDupEmailCodeData();
    //    $fixture->setContainer($container);

    //    $loader = new Loader();
    //    $loader->addFixture($fixture);

    //    $executor->execute($loader->getFixtures());
        $this->container = $container;
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
     * @group issue_474
     * @group  debug
     */
    public function testCallBackAction() 
    {
        $client = static::createClient();
        $url = $this->container->get('router')->generate('qq_api_callback');
        $this->assertEquals('/QQLogin/qqcallback', $url);

        $url = $this->container->get('router')->generate('qq_api_callback');
        $this->assertEquals('/QQLogin/qqcallback', $url);

    }
    /**
     * @group issue_474
     * @group  debug
     */
    public function testqqLoginAction()
    {
        $url = $this->container->get('router')->generate('qq_api_login');
        $this->assertEquals('/QQLogin/qqlogin', $url);
    }

    /**
     * @group issue_474
     * @group  debug
     */
    public function testqqRegisteAction()
    {
        $url = $this->container->get('router')->generate('qq_registe');
        $this->assertEquals('/QQLogin/qqRegiste', $url);
    }

    /**
     * @group issue_474
     * @group  debug
     */
   public function testqqBindAction()
    {
        $url = $this->container->get('router')->generate('qq_bind');
        $this->assertEquals('/QQLogin/qqbind', $url);
    }

    /**
     * @group issue_474
     * @group  debug
     */
    public function testqqFirstLoginAction()
    {
        $url = $this->container->get('router')->generate('qq_fist_login');
        $this->assertEquals('/QQLogin/qqFistLogin', $url);
    }
}
