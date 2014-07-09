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

    public function testGetSingleUserPointForJulyActivity() {
        $start = "2014-07-01";
        $end = "2014-07-31";
        $user_id = 1057704;
        $myInfo = $this->em->getRepository('JiliApiBundle:User')->getSingleUserPointForJulyActivity($start, $end, $user_id);
        $this->assertEquals(80, $myInfo[0]['points']);
    }

    public function testGetUserPointForJulyActivity() {
        $start = "2014-07-01";
        $end = "2014-07-31";

        //前100名
        $limit = 100;
        $offset = 0;
        $users = $this->em->getRepository('JiliApiBundle:User')->getUserPointForJulyActivity($start, $end, $limit, $offset);
        $this->assertEquals(100, count($users));
    }
}