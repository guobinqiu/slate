<?php
namespace Jili\ApiBundle\Tests\Entity;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryFunctionalTest extends WebTestCase {

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

        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    public function testGetUserCPAPointsByTime() {
        $start = "2014-07-01 00:00:00";
        $end = "2014-07-31 23:59:59";
        $user_id = 1173775;
        $myInfo = $this->em->getRepository('JiliApiBundle:User')->getUserCPAPointsByTime($start, $end, $user_id);
        $this->assertEquals(1000, $myInfo[0]['points']);
    }

    public function testGetTotalCPAPointsByTime() {
        $start = "2014-07-01 00:00:00";
        $end = "2014-07-31 23:59:59";

        //前100名
        $limit = 100;
        $offset = 0;
        $users = $this->em->getRepository('JiliApiBundle:User')->getTotalCPAPointsByTime($start, $end, $limit, $offset);
        $this->assertEquals(100, count($users));
    }
}