<?php
namespace Jili\ApiBundle\Tests\Services;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\Services\LoadUserLoginData;

class UserLoginTest extends KernelTestCase
{


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $cotainer;


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
        $container  = static::$kernel->getContainer();
        $this->container = $container;

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();
        $tn = $this->getName();
        if( $tn =='testDoLogin' ) {

            // load fixtures
            $fixture = new LoadUserLoginData();

            $loader = new Loader();
            $loader->addFixture($fixture);

            $executor->execute($loader->getFixtures());

        }

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
     *  @group debug
     */
    public function testContainerGet()
    {
        $container = $this->container;
        $login_service = $container->get('login.listener');
        $this->assertInstanceOf('Jili\\ApiBundle\\Services\\UserLogin',$login_service, 'login listener is instance of  Jili\\ApiBundle\\Services\\UserLogin');
    }

    public function testDoLogin() 
    {
        $container = $this->container;
        $result = $container->get('login.listener')
            ->doLogin(array(
                'email'=> 'alice.nima@voyagegroup.com.cn',
                'pwd'=>'111111' ,
                'method'=> 'POST',
                'client_ip'=> '127.0.0.1'
            ));

        $this->assertEquals('ok', $result,  '"ok" for alice login successuflly');

        $result = $container->get('login.listener')
            ->doLogin(array(
                'email'=> 'bob.inch@voyagegroup.com.cn',
                'pwd'=>'123123' ,
                'method'=> 'POST',
                'client_ip'=> '127.0.0.1'
            ));
        $this->assertEquals('ok', $result,  '"ok" for bob login successuflly');

    }
}
