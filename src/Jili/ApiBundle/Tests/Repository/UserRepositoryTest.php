<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class UserRepositoryTest extends KernelTestCase
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
     * @group point_recent
     */
    public function testGetRecentPoint()
    {
        $em = $this->em;
        $date_str = '2014-03-04';
        $result = $em->getRepository('JiliApiBundle:User')->getRecentPoint($date_str);

        $this->assertCount(99, $result);
        $this->assertEquals('565a2bc39cd6621d84173f7ee11ee991',md5(serialize($result)));
    }
    /**
     * @group debug
     * @group issue_448
     */
    public function testCreateOnSignup() {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

    }
}
