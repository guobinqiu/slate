<?php
namespace Jili\ApiBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\BrowserKit\Cookie;

class UserSignUpTracerTest extends KernelTestCase
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
     */
    public function testLog() {
        $signUpTracer = static::$kernel->getContainer()->get('user_sign_up_route.listener') ;

        $logger  = static::$kernel->getContainer()->get('logger');

        $request = new Request();
        $cn = get_class($request);

        $cm = get_class_methods($cn);
        $logger->debug('{jarod}'. implode(':', array(__CLASS__, __LINE__, '') ). $cn ) ;
        $logger->debug('{jarod}'. implode(':', array(__CLASS__, __LINE__, '') ). var_export($cm, true) ) ;

        // $cookie = new Cookie('jili_rememberme', $token, time() + 3600 * 24 * 365, '/', null, false, false);
        // $client->getCookieJar()->set($cookie);
        // how to set cookie in to 

        $this->assertEquals(1,'1');

    }
}
