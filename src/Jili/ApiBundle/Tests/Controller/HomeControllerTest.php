<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
/**
 * 
 **/
class HomeControllerTest extends WebTestCase
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
//        $test_name = $this->getName();
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
     * @group issue_437
     */
    public function testVoteAction() 
    {
        // write testing data 


        
        $client = static::createClient();
        $container = $client->getContainer();
        $router = $container->get('router');
        $logger= $container->get('logger');

        // request the url
        
        // check the response html page.
        
        // check the link with token. 
        
        // check the link without token. 

    }

}

