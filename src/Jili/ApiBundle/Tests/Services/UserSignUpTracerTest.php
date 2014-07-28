<?php
namespace Jili\ApiBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

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
    public function testLogWhenEmtpySession() {
        $container  = static::$kernel->getContainer();


        $log_path = $container->getParameter('kernel.logs_dir');
        $log_path .= '/'.$container->getParameter('kernel.environment');
        $log_path .= '.user_source.log';

        if( file_exists( $log_path)) {
            @unlink( $log_path);
        }

        $signUpTracer = $container->get('user_sign_up_route.listener') ;

        $logger  = $container->get('logger');
        $spm  = '';

        // the ssession is for unique token id. 
        $session = $container->get('session');
        $session->set('id', '1234567890');
        $session->save();

        $signUpTracer->log( );

// 2014-07-28 09:41:47	1793cf06-a5bd1aa1	user_source	INFO	baidu_partnerc
        // %kernel.logs_dir%/%kernel.environment%.user_source.log

        $this->assertFileNotExists($log_path, 'check log file exits');

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
        $spm  = '';

        // the ssession is for unique token id. 
        $session = $container->get('session');
        $session->set('id', '1234567890');
        $session->set('source_route', 'baidu_partnerc');
        $session->save();

        $signUpTracer->log( );

// 2014-07-28 09:41:47	1793cf06-a5bd1aa1	user_source	INFO	baidu_partnerc
        // %kernel.logs_dir%/%kernel.environment%.user_source.log
        $log_path = $container->getParameter('kernel.logs_dir');
        $log_path .= '/'.$container->getParameter('kernel.environment');
        $log_path .= '.user_source.log';

        $this->assertFileExists($log_path, 'check log file exits');

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

        $this->assertCount(5,$arr, 'check the content of log file');
        $this->assertEquals( 'user_source',$arr[2], 'check the content of log file');
        $this->assertEquals( $session->get('source_route')  ,$arr[4], 'check the content of log file');

        // todo: test with wild cookies value for security
    }

    /**
     * There is no sign row in table when not key of 'source_route' in sessions
     * @group debug  
     * @group issue_396  
     * @group signup_trace 
     */
    public function testSignedWithSessionEmpty() {
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

        $signUpTracer->signed(array( 'user_id'=> $user->getId()) );
        // order by id desc  
        $records = $em->getRepository('JiliApiBundle:UserSignUpRoute')->findAll();
        $this->assertCount( 0,$records, 'check the user_source_logger table');
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
        $session->set('source_route', 'baidu_partnerd');
        $session->save();

        $signUpTracer->signed(array( 'user_id'=> $user->getId()) );
        // order by id desc  
        $records = $em->getRepository('JiliApiBundle:UserSignUpRoute')->findAll();

        $this->assertCount( 1,$records, 'check the user_source_logger table');

        $this->assertEquals( $user->getId() ,$records[0]->getUserId(), 'check the user_source_logger table');
        $this->assertEquals( $session->get('source_route') ,$records[0]->getSourceRoute(), 'check the user_source_logger table');

        
    }
    /**
     * @group debug  
     * @group issue_396  
     * @group signup_trace 
     */
    public function testRefreshRouteSession() 
    {
        $container  = static::$kernel->getContainer();
        $signUpTracer = $container->get('user_sign_up_route.listener') ;

        $session=$container->get('session');
        $session->clear();

        $session->set('id', '1234567890');
        $session->save();

        $signUpTracer->refreshRouteSession(array());
        $session =  $container->get('session');
        $this->assertEmpty( $session->get('source_route'));

        $signUpTracer->refreshRouteSession(array('spm'=>'baidu_partnere'));
        $session =  $container->get('session');
        $this->assertEquals( 'baidu_partnere',$session->get('source_route'));
    }

    /**
     * @group debug  
     * @group issue_396  
     * @group signup_trace 
     */
    public function testGetRouteSession() {

        $container  = static::$kernel->getContainer();
        $signUpTracer = $container->get('user_sign_up_route.listener') ;

        $session=$container->get('session');
        $session->clear();

        $this->assertEmpty( $signUpTracer->getRouteSession());

        $session->set('id', '1234567890');
        $session->set('source_route', 'baidu_partnerf');
        $session->save();

        $this->assertEquals('baidu_partnerf', $signUpTracer->getRouteSession());
    }

}
