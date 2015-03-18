<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\FrontendBundle\DataFixtures\ORM\Repository\GameSeekerDaily\LoadGetInfoByUserData;

class GameSeekerDailyRepositoryTest  extends KernelTestCase 
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container  = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $fixture = new LoadGetInfoByUserData();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->purge();
        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_524 
     */
    public function testGetInfoByUser() {
        $em = $this->em;
        $today = new \DateTime();
        $today->setTime(0,0);

        //case i, a user not clickedToday, the request will insert a new record.
        $instance = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=>1, 'clickedDay'=>$today  ));
        $this->assertNull($instance,'before get, not exist');

        $instance = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->getInfoByUser(1);
        $instance_after = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=>1, 'clickedDay'=>$today));
        $this->assertNotNull($instance_after,'after get, exists');
        $this->assertInstanceOf('Jili\\FrontendBundle\\Entity\\GameSeekerDaily', $instance_after);
        $this->assertSame($instance_after, $instance,'should be the same one');

        // case ii. a user has open the page but not click today.
        $instance_before = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=>10, 'clickedDay'=>$today ));
        $before_token= $instance_before->getToken();
        $before_token_updated_at = $instance_before->getTokenUpdatedAt();

        $this->assertNotNull($instance_before,'before get, exist');
        $this->assertInstanceOf('Jili\\FrontendBundle\\Entity\\GameSeekerDaily', $instance_before, 'before get');

        $instance = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->getInfoByUser(10);
        $token = $instance->getToken();
        $token_updated_at = $instance->getTokenUpdatedAt();

        $instance_after  = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=>10, 'clickedDay'=>$today ));

        $this->assertNotNull($instance_after,'after get, exists');
        $this->assertInstanceOf('Jili\\FrontendBundle\\Entity\\GameSeekerDaily', $instance_after);

        $after_token= $instance_after->getToken();
        $after_token_updated_at = $instance_after->getTokenUpdatedAt();

        $this->assertNotEquals( $before_token, $after_token, 'token should be diff');

        // case iii. a user has clicked today and finished. 
        
        $instance_before = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=>10, 'clickedDay'=>$today ));
        $before_token= $instance_before->getToken();
        $before_token_updated_at = $instance_before->getTokenUpdatedAt();

        $this->assertNotNull($instance_before,'before get, exist');
        $this->assertInstanceOf('Jili\\FrontendBundle\\Entity\\GameSeekerDaily', $instance_before, 'before get');

        $instance = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->getInfoByUser(21);
        $this->assertNull($instance);

        $instance_after  = $em->getRepository('JiliFrontendBundle:GameSeekerDaily')->findOneBy(array('userId'=>21, 'clickedDay'=>$today ));

        $this->assertNotNull(serialize($instance_before), serialize($instance_after));
    }
}
