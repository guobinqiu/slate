<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserRepositoryTest extends WebTestCase
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
     * @group debug 
     * @group point_recent
     */
    public function testGetRecentPoint() 
    {
        $em = $this->em;
        $date_str = '2014-03-04';
		$result = $em->getRepository('JiliApiBundle:User')->getRecentPoint($date_str);

$this->assertEquals(1,'1');
    }
}
