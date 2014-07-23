<?php
namespace Jili\ApiBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\BrowserKit\Cookie;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadLandingTracerCodeData;

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
     * @group issue_396  
     * @group signup_trace 
     */
    public function testLog() {
        $container  = static::$kernel->getContainer();
        $signUpTracer = $container->get('user_sign_up_route.listener') ;

        $logger  = $container->get('logger');

        // the ssession is for unique token id. 
        $session = $container->get('session');
        $session->set('id', '1234567890');
        $session->save();

        // how to set cookie in to 
        $cookies = new \Symfony\Component\HttpFoundation\ParameterBag();
        $cookies->set('source_route', 'baidu_partnera');
        $time = time();
        $cookies->set('pv', hash( 'ripemd160','baidu_partnera'. $time));
        $cookies->set('pv_unique', hash('md5','baidu_partnera'. $time));

        $request = new Request();
        $request->cookies = $cookies;

        $signUpTracer->log($request);

        // %kernel.logs_dir%/%kernel.environment%.user_source.log
        $log_path = $container->getParameter('kernel.logs_dir');
        $log_path .= '/'.$container->getParameter('kernel.environment');
        $log_path .= '.user_source.log';

        $this->assertFileExists($log_path, 'check log file exits');

        // 2014-07-23 13:01:50	0ea59ced-1be687d5	user_source	INFO	baidu_partnera	97f484631f9142218eac41dddde0aa22a5036ce6	bc98e0284dbf6f1c6518fd4e070ba9a4
        // 2014-07-23 13:08:51	97428563-3634a26a	user_source	INFO	baidu_partnera	e93ec06cfa7cfcec737b489212ad658a50a6755b	fd0abd9555a7bff708786a67f9f97094

        // fetch the last line of the file.

        $fp = fopen($log_path, 'r');
        fseek($fp, -2, SEEK_END); 
        $pos = ftell($fp);
        fseek($fp, $pos--);

        $last_row ='';
        // Loop backword util "\n" is found.
        while((($c = fgetc($fp)) != "\n") && ($pos > 0)) {
            $last_row= $c.$last_row;
            fseek($fp, $pos--);
        }
        fclose($fp);

        $arr = explode("\t", $last_row);

        $this->assertCount(7,$arr, 'check the content of log file');
        $this->assertEquals( 'user_source',$arr[2], 'check the content of log file');
        $this->assertEquals( $cookies->get('source_route')  ,$arr[4], 'check the content of log file');
        $this->assertEquals( $cookies->get('pv')  ,$arr[5], 'check the content of log file');
        $this->assertEquals( $cookies->get('pv_unique')  ,$arr[6], 'check the content of log file');

        // todo: test with wild cookies value for security
    }
    /**
     * @group debug  
     * @group issue_396  
     * @group signup_trace 
     */
    public function testSigned() {
        $container  = static::$kernel->getContainer();
        $signUpTracer = $container->get('user_sign_up_route.listener') ;

        $em = $this->em;
        $logger= $container->get('logger');

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadLandingTracerCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

        $user = LoadLandingTracerCodeData::$USER[0];
        // the ssession is for unique token id. 
        $session = $container->get('session');
        $session->set('id', '1234567890');
        $session->save();

        // how to set cookie in to 
        $cookies = new \Symfony\Component\HttpFoundation\ParameterBag();
        $cookies->set('source_route', 'baidu_partnera');
        $time = time();
        $cookies->set('pv', hash( 'ripemd160','baidu_partnera'. $time));
        $cookies->set('pv_unique', hash('md5','baidu_partnera'. $time));

        $request = new Request();
        $request->cookies = $cookies;

        $signUpTracer->signed($request, $user);

        // order by id desc  
        $records = $em->getRepository('JiliApiBundle:UserSignUpRoute')->findAll();

        $this->assertCount( 1,$records, 'check the user_source_logger table');

        $this->assertEquals( $user->getId() ,$records[0]->getUserId(), 'check the user_source_logger table');
        $this->assertEquals( $cookies->get('source_route') ,$records[0]->getSourceRoute(), 'check the user_source_logger table');
        //todo: ?
    }
}
