<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

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
     */
    public function test91wenwenRegister5()
    {
        $client = static :: createClient();

        $url ='/api/91wenwen/register';
        $email = 'zhangmm@voyagegroup.com.cn';
        $crawler = $client->request('POST',$url, array (
            'email' => $email,
            'signature' => '88ed4ef124e926ea1df1ea6cdddf8377771327ab',
            'uniqkey' => 'test'
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);

        $em = $this->em;
        $user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);;
        $user->getId();
        $record =  $em->getRepository('JiliApiBundle:SeTPasswordCode')->findBy( array('userId'=> $user->getId()) );
        $this->assertCount(1, $record,' checkin point setPassword code');

        $expected = '{"status":"1","message":"success","activation_url":"https:\/\/www.91jili.com\/user\/setPassFromWenwen\/'.$record[0]->getCode() .'\/'.$user->getId() .'"}';

        $this->assertEquals($expected,$client->getResponse()->getContent() );

    }
}
