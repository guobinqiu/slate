<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadProfileData;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Wenwen\FrontendBundle\Controller\ProfileController;

class ProfileControllerTest extends WebTestCase
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
        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new LoadProfileData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->container = $container;
        $this->em = $em;

        @session_start();
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
     * @group dev-merge-ui-set-password
     */
    public function testChangePwdAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_profile_changepwd');

        $session = $container->get('session');
        $session->remove('uid');
        $session->save();

        //don't login
        $post_data = array ();
        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Need login', $client->getResponse()->getContent());

        //set login id
        $session = $container->get('session');
        $session->set('uid', 1);
        $session->save();

        // csrf not valiad
        $post_data = array ();
        $post_data['csrf_token'] = 123;

        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Access Forbidden', $client->getResponse()->getContent());

        //set csrf token
        $csrfProvider = new DefaultCsrfProvider('SECRET');
        $csrf_token = $csrfProvider->generateCsrfToken('profile');
        $session = $container->get('session');
        $session->set('csrf_token', $csrf_token);
        $session->save();
        $this->assertTrue($session->has('csrf_token'));

        // csrf is valiad , has other error
        $post_data = array ();
        $post_data['curPwd'] = '';
        $post_data['pwd'] = '';
        $post_data['pwdRepeat'] = '';
        $post_data['csrf_token'] = $csrf_token;

        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('请输入旧的用户密码', $client->getResponse()->getContent());

        // csrf is valiad , no error
        $post_data = array ();
        $post_data['curPwd'] = '111111';
        $post_data['pwd'] = '222222';
        $post_data['pwdRepeat'] = '222222';
        $post_data['csrf_token'] = $csrf_token;

        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('密码修改成功', $client->getResponse()->getContent());

        //确认密码修改成功
        $em = $this->em;
        $user = $em->getRepository('JiliApiBundle:User')->find(1);
        $this->assertTrue($user->isPwdCorrect('222222'));
    }

    /**
     * @group dev-merge-ui-set-password
     */
    public function testCheckPassword()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $controller = new ProfileController();
        $controller->setContainer($container);

        $id = 1;

        $curPwd = '';
        $pwd = '';
        $pwdRepeat = '';

        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('请输入旧的用户密码', $return);

        $curPwd = '123';
        $pwd = '';
        $pwdRepeat = '';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('请输入新的用户密码', $return);

        $curPwd = '123';
        $pwd = '123';
        $pwdRepeat = '';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('请输入新的用户密码', $return);

        $curPwd = '123';
        $pwd = '123';
        $pwdRepeat = '456';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('2次输入的用户密码不相同', $return);

        $curPwd = '123';
        $pwd = '123';
        $pwdRepeat = '123';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('用户密码为6-20个字符，不能含特殊符号', $return);

        $curPwd = '123';
        $pwd = '111111';
        $pwdRepeat = '111111';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('旧密码不正确', $return);

        // 旧密码正确, jili密码
        $curPwd = '111111';
        $pwd = '222222';
        $pwdRepeat = '222222';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertFalse($return);

        // 旧密码不正确, UserWenwenLogin不存在, wenwen密码
        $id = 3;
        $curPwd = '123456';
        $pwd = '222222';
        $pwdRepeat = '222222';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('旧密码不正确', $return);

        // 旧密码不正确, UserWenwenLogin存在, wenwen密码不正确
        $id = 2;
        $curPwd = '123456';
        $pwd = '222222';
        $pwdRepeat = '222222';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('旧密码不正确', $return);

        // 旧密码正确, wenwen密码
        $id = 2;
        $curPwd = '111111';
        $pwd = '222222';
        $pwdRepeat = '222222';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertFalse($return);
    }
}
