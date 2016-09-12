<?php
namespace Jili\ApiBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixtureLoader;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserEdmUnsubscribeData;

class UserEdmUnsubscribeRepositoryTest extends KernelTestCase {

    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserEdmUnsubscribeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_450
     */
    public function testFindByEmail() {
        $em = $this->em;

        $email = null;
        $return = $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->findByEmail($email);
        $this->assertEquals('2', count($return));

        $email = 'zhangmm@voyagegroup.com.cn';
        $return = $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->findByEmail($email);
        $this->assertEquals('1', count($return));
        $this->assertEquals($email, $return[0]['email']);
    }

    public function testInsertOne() 
    {
        $em = $this->em;
        $return = $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->insertOne('aaa');
        $this->assertNull($return);
        $return = $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->insertOne(-1);
        $this->assertNull($return);
        $return = $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->insertOne(0);
        $this->assertNull($return);

        $return = $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->insertOne(1000);
        $return = $em->getRepository('WenwenFrontendBundle:UserEdmUnsubscribe')->findByUserId(1000);
        $this->assertEquals('1', count($return));
    }

}
