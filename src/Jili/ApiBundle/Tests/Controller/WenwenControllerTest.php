<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadWenwenRegister5CodeData;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;
use Jili\ApiBundle\Utility\WenwenToken;

class WenwenControllerTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $loader = new Loader();

        $fixture = new LoadWenwenRegister5CodeData();
        $fixture->setContainer($container);
        $loader->addFixture($fixture);

        $fixture = new LoadUserData();
        $fixture->setContainer($container);
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister() {
        $client = static :: createClient();
        $url = '/api/91wenwen/register';
        $crawler = $client->request('POST', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"missing email"}', $client->getResponse()->getContent());
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister1() {
        $client = static :: createClient();
        $url = '/api/91wenwen/register';
        $crawler = $client->request('POST', $url, array (
            'email' => '',
            'signature' => '',
            'uniqkey' => ''
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"missing email"}', $client->getResponse()->getContent());
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister2() {
        $client = static :: createClient();

        $url = '/api/91wenwen/register';
        $crawler = $client->request('POST', $url, array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '',
            'uniqkey' => ''
        ));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"missing signature"}', $client->getResponse()->getContent());
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister3() {
        $client = static :: createClient();
        $url = '/api/91wenwen/register';
        $crawler = $client->request('POST', $url, array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '11',
            'uniqkey' => ''
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"missing uniqkey"}', $client->getResponse()->getContent());
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister4() {
        $client = static :: createClient();
        $url = '/api/91wenwen/register';
        $crawler = $client->request('POST', $url, array (
            'email' => 'zhangmm@voyagegroup.com.cn',
            'signature' => '11',
            'uniqkey' => 'test'
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);
        $this->assertEquals('{"status":"0","message":"access error "}', $client->getResponse()->getContent());
    }

    /**
     * @group user
     * @group wenwenuser
     * @group signup
     */
    public function test91wenwenRegister5() {
        $em = $this->em;
        $client = static :: createClient();
        $container = static :: $kernel->getContainer();

        $url = '/api/91wenwen/register';
        $user = LoadWenwenRegister5CodeData :: $ROWS[0];
        $email = $user->getEmail();
        $crawler = $client->request('POST', $url, array (
            'email' => $email,
            'signature' => '88ed4ef124e926ea1df1ea6cdddf8377771327ab',
            'uniqkey' => 'test'
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'post to ' . $url);

        //$user = $em->getRepository('JiliApiBundle:User')->findOneByEmail($email);;
        // $user->getId();
        $record = $em->getRepository('JiliApiBundle:SetPasswordCode')->findBy(array (
            'userId' => $user->getId()
        ));
        $this->assertCount(1, $record, ' checkin point setPassword code');

        $wenwen_api_url = $container->getParameter('91wenwen_api_url');
        $expected['status'] = "1";
        $expected['message'] = "success";
        $expected['activation_url'] = $wenwen_api_url . '/user/setPassFromWenwen/' . $record[0]->getCode() . '/' . $user->getId();

        $content = $client->getResponse()->getContent();

        $this->assertEquals(json_encode($expected), $content);
    }

    /**
     * @group issue_487
     * @group accountBindAction
     */
    public function testAccountBindAction() {
        $em = $this->em;
        $user = LoadUserData :: $USERS[0];

        $client = static :: createClient();
        $container = static :: $kernel->getContainer();
        $session = $container->get('session');

        $router = $container->get('router');

        //$url = $router->generate('_account_bind', array ('state' => '123'), false);
        $url = '/api/91wenwen/bind/123';

        // not login
        $session->remove('uid', '');
        $session->save();
        $this->assertFalse($session->has('uid'));
        $crawler = $client->request('POST', $url);
        $this->assertEquals('/api/91wenwen/bind/123', $client->getRequest()->getRequestUri());

        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        // check the redirected url.
        //$this->assertEquals('/user/login', $client->getRequest()->getRequestUri());
        $this->assertEquals('/api/91wenwen/bind/123', $client->getRequest()->getRequestUri());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        // no data in db
        $cross = $em->getRepository('JiliApiBundle:UserWenwenCross')->findOneByUserId($user->getId()+100);
        $this->assertNull($cross);

        // login
        $session->set('uid', $user->getId());
        $session->save();
        $this->assertTrue($session->has('uid'));
        $this->assertEquals($user->getId(), $session->get('uid'));

        $crawler = $client->request('POST', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // has data in db
        $cross = $em->getRepository('JiliApiBundle:UserWenwenCross')->findOneByUserId($user->getId());
        $this->assertNotNull($cross);
        $crossToken = $em->getRepository('JiliApiBundle:UserWenwenCrossToken')->findOneByCrossId($cross->getId());
        $this->assertNotNull($crossToken);

        // connect_url
        $wenwen_api_connect_jili = $container->getParameter('91wenwen_api_connect_jili');
        $connect_url = $wenwen_api_connect_jili . '?state=123&token=' . $crossToken->getToken();
        $link_node = $crawler->filter('a')->eq(1);
        $link = $link_node->link();
        $this->assertEquals($connect_url, $link->getUri(), 'Check wenwen bind page url');
    }

    /**
    * @group issue_487
    * @group accountBindApiAction
    */
    public function testAccountBindApiAction() {
        $em = $this->em;
        $user = LoadUserData :: $USERS[0];

        $client = static :: createClient();
        $container = static :: $kernel->getContainer();

        $router = $container->get('router');

        $url = '/api/91wenwen/bindApi';

        $secret_key = $container->getParameter('91wewen_bind_secret_key');

        // signature invalid
        $time = time();
        $token = 'a4d3d591c343d3c6aae70ad8b492171e3bce6aa6232b0858540713906e0d68ff';
        $params = array (
            'token' => $token,
            'time' => $time
        );
        $signature = WenwenToken :: createSignature($params, $secret_key);
        $post_data = array (
            'token' => $token,
            'time' => $time,
            'signature' => $signature . 'invalid'
        );
        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals('/api/91wenwen/bindApi', $client->getRequest()->getRequestUri());
        $this->assertEquals(301, $client->getResponse()->getStatusCode()); //todo 301
        $crawler = $client->followRedirect();
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $res = json_decode($client->getResponse()->getContent(), true);
        $expected = array (
            'meta' => array (
                'code' => 400,
                'message' => 'signature invalid'
            )
        );
        $this->assertEquals($expected, $res);

        // token not exist
        $token = 'a4d3d591c343d3c6aae70ad8b492171e3bce6aa6232b0858540713906e0d68ff';
        $params = array (
            'token' => $token,
            'time' => $time
        );
        $signature = WenwenToken :: createSignature($params, $secret_key);
        $post_data = array (
            'token' => $token,
            'time' => $time,
            'signature' => $signature
        );
        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals('/api/91wenwen/bindApi', $client->getRequest()->getRequestUri());
        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $res = json_decode($client->getResponse()->getContent(), true);
        $expected = array (
            'meta' => array (
                'code' => 400,
                'message' => 'token not exist'
            )
        );
        $this->assertEquals($expected, $res);

        $cross = $em->getRepository('JiliApiBundle:UserWenwenCross')->create($user->getId());
        $crossToken = $em->getRepository('JiliApiBundle:UserWenwenCrossToken')->create($cross->getId());
        $params = array (
            'token' => $crossToken->getToken(),
            'time' => $time
        );
        $signature = WenwenToken :: createSignature($params, $secret_key);
        $post_data = array (
            'token' => $crossToken->getToken(),
            'time' => $time,
            'signature' => $signature
        );
        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals('/api/91wenwen/bindApi', $client->getRequest()->getRequestUri());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $time = time();
        $params = array (
            'cross_id' => $cross->getId(),
            'time' => $time
        );
        $signature_send = WenwenToken :: createSignature($params, $secret_key);

        $res = json_decode($client->getResponse()->getContent(), true);
        $expected = array (
            'meta' => array (
                'code' => 200
            ),
            'data' => array (
                'cross_id' => $cross->getId(),
                'time' => $time,
                'signature' => $signature_send
            )
        );
        $this->assertEquals($expected, $res);
    }
}
