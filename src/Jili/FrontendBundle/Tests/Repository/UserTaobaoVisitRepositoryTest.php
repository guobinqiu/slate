<?php
namespace Jili\FrontendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
use Jili\FrontendBundle\Entity\UserTaobaoVisit;

class UserTaobaoVisitRepositoryTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $user;

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
        $fixture = new LoadUserData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());
        $this->user = LoadUserData :: $USERS[0];

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
     * @group issue_504
     */
    public function testGetTaobaoVisit() {
        $em = $this->em;
        $day = date('Ymd');
        $user_id = $this->user->getId();
        $visit = $em->getRepository('JiliFrontendBundle:UserTaobaoVisit')->getTaobaoVisit($user_id, $day);
        $this->assertEmpty($visit);

        $visit = new UserTaobaoVisit();
        $visit->setUserId($user_id);
        $visit->setVisitDate($day);
        $em->persist($visit);
        $em->flush();

        $this->assertTrue(!empty ($visit));
    }
}