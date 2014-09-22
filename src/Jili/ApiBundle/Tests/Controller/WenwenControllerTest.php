<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadWenwenRegister5CodeData;

class WenwenControllerTest extends WebTestCase
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
        $tn = $this->getName(); 
        $container  = static::$kernel->getContainer();
        if (in_array( $tn ,array('test91wenwenRegister5'))) {
            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();
            $loader = new Loader();
            $fixture  = new LoadWenwenRegister5CodeData();

            $fixture->setContainer($container);
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());
       // add an user 
        }


        $this->em  = $em;
        $this->container  = $container;
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
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister()
    {
        $client = static :: createClient();
        $url ='/api/91wenwen/register';
        $crawler = $client->request('POST',$url );
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"missing email"}',$client->getResponse()->getContent() );
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister1()
    {
        $client = static :: createClient();
        $url = '/api/91wenwen/register';
        $crawler = $client->request('POST', $url, array (
            'email' => '',
            'signature' => '',
            'uniqkey' => ''
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"missing email"}',$client->getResponse()->getContent() );
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister2()
    {
        $client = static :: createClient();

        $url ='/api/91wenwen/register';
        $crawler = $client->request('POST', $url, array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '',
            'uniqkey' => ''
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"missing signature"}',$client->getResponse()->getContent() );
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister3()
    {
        $client = static :: createClient();
        $url =  '/api/91wenwen/register';
        $crawler = $client->request('POST',$url, array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '11',
            'uniqkey' => ''
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"missing uniqkey"}',$client->getResponse()->getContent() );
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister4()
    {
        $client = static :: createClient();
        $url =  '/api/91wenwen/register';
        $crawler = $client->request('POST',$url, array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '11',
            'uniqkey' => 'test'
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"access error "}',$client->getResponse()->getContent() );
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     * @group debug 
     */
    public function test91wenwenRegister5()
    {
        $em = $this->em;
        $client = static :: createClient();

        $url ='/api/91wenwen/register';
        $user = LoadWenwenRegister5CodeData::$ROWS[0];

        $email = $user->getEmail();

        $crawler = $client->request('POST', $url, array (
            'email' => $email,
            'signature' => '88ed4ef124e926ea1df1ea6cdddf8377771327ab',
            'uniqkey' => 'test'
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);

        //$user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);;
       // $user->getId();
        $record =  $em->getRepository('JiliApiBundle:SetPasswordCode')->findBy( array('userId'=> $user->getId()) );
        $this->assertCount(1, $record,' checkin point setPassword code');

        $expected = '{"status":"1","message":"success","activation_url":"https:\/\/www.91jili.com\/user\/setPassFromWenwen\/'.$record[0]->getCode() .'\/'.$user->getId() .'"}';

        $this->assertEquals($expected,$client->getResponse()->getContent() );

    }
}
