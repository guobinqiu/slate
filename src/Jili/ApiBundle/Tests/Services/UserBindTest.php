<?php
namespace Jili\ApiBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\Services\LoadUserBindData;

class UserBindTest extends KernelTestCase
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


        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $fixture = new LoadUserBindData();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

$this->container = static::$kernel->getContainer();
        $this->em = $em;
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
     */
    public function testqq_user_bind() 
    {

        // if (isset($params['email']) && isset($params['open_id'])) {
        // 1.1 email , open_id
        $params = array('email'=>1); 
        $qqUser =  $this->container->get('user_bind')->qq_user_bind($params);

        // 1.2 
        $params = array('open_id'=>1); 
        $qqUser =  $this->container->get('user_bind')->qq_user_bind($params);

        $user = LoadUserBindData::$USERS[0];
            // if( $user) {
        // 2.1 no user
        
        $params = array('email'=> 1, 'open_id'=>1); 
        $qqUser =  $this->container->get('user_bind')->qq_user_bind($params);
        $this->assertNull($qqUser);

        // 2.2 has user
        $params = array('email'=> $user->getEmail(), 'open_id'=>1); 
        $qqUser =  $this->container->get('user_bind')->qq_user_bind($params);
        $this->assertNotNull($qqUser);

        $qq_user = $this->em->getRepository('JiliApiBundle:QQUser')->findOneBy(array('userId'=> $user->getId(),
        'openId'=> 1));
        $this->assertNotNull($qq_user);
    }

}
