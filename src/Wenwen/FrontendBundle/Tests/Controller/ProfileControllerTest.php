<?php
namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
    public function testIndexAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_profile_index');
        $crawler = $client->request('GET', $url);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $session = $container->get('session');

        //login 后
        $session->set('uid', 1);
        $session->save();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains($session->get('csrf_token'), $client->getResponse()->getContent());
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
        $this->assertEquals('{"status":0,"message":"Need login"}', $client->getResponse()->getContent());

        //set login id
        $session = $container->get('session');
        $session->set('uid', 1);
        $session->save();

        // csrf not valiad
        $post_data = array ();
        $post_data['csrf_token'] = 123;

        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('{"status":0,"message":"Access Forbidden"}', $client->getResponse()->getContent());

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

        $crawler = $client->request('GET', $url, $post_data);
        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $return = $client->getResponse()->getContent();
        $return = json_decode($return, true);
        $this->assertEquals(0, $return['status']);
        $this->assertEquals('请输入旧的用户密码', $return['message']);

        // csrf is valiad , no error
        $post_data = array ();
        $post_data['curPwd'] = '111111';
        $post_data['pwd'] = '22222q';
        $post_data['pwdRepeat'] = '22222q';
        $post_data['csrf_token'] = $csrf_token;

        $crawler = $client->request('POST', $url, $post_data);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $return = $client->getResponse()->getContent();
        $return = json_decode($return, true);
        $this->assertEquals(1, $return['status']);
        $this->assertEquals('密码修改成功', $return['message']);

        //确认密码修改成功
        $em = $this->em;
        $user = $em->getRepository('JiliApiBundle:User')->find(1);
        $this->assertTrue($user->isPwdCorrect('22222q'));
    }

    /**
     * @group dev-merge-ui-set-password
     * @group dev-merge-ui-profile-edit
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
        $this->assertEquals('用户密码为5-100个字符，密码至少包含1位字母和1位数字', $return);

        $curPwd = '123';
        $pwd = '11111a';
        $pwdRepeat = '11111a';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('旧密码不正确', $return);

        // 旧密码正确, jili密码
        $curPwd = '111111';
        $pwd = '22222a';
        $pwdRepeat = '22222a';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertFalse($return);

        // 旧密码不正确, UserWenwenLogin不存在, wenwen密码
        $id = 3;
        $curPwd = '123456';
        $pwd = '22222a';
        $pwdRepeat = '22222a';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('旧密码不正确', $return);

        // 旧密码不正确, UserWenwenLogin存在, wenwen密码不正确
        $id = 2;
        $curPwd = '123456';
        $pwd = '22222a';
        $pwdRepeat = '22222a';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertEquals('旧密码不正确', $return);

        // 旧密码正确, wenwen密码
        $id = 2;
        $curPwd = '111111';
        $pwd = '22222a';
        $pwdRepeat = '22222a';
        $return = $controller->checkPassword($curPwd, $pwd, $pwdRepeat, $id);
        $this->assertFalse($return);
    }

    /**
     * @group dev-merge-ui-profile-edit
     * @group dev-merge-ui-profile-nick
     * @group dev-merge-ui-profile-sex
     */
    public function testEditProfile()
    {
        $client = static::createClient();

        $url = '/profile/edit';

        //没有登录
        $crawler = $client->request('GET', $url);
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        //login, 用户 存在
        $session = $client->getRequest()->getSession();
        $session->set('uid', 1);
        $session->save();

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->filter('form[name=profileForm]')->form();

        // set some values
        $form['profile[nick]'] = 'aaaa';
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertTrue($crawler->filter('html:contains("用户昵称已经存在")')->count() > 0);

        $form['profile[nick]'] = 'nick';
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->em->clear();
        $user = $this->em->getRepository('JiliApiBundle:User')->find(1);
        $this->assertEquals('nick', $user->getNick());
        $this->assertEquals(null, $user->getSex(), 'if user don\'t choose sex,will be set null');

        $form['profile[nick]'] = 'nick';
        $form['profile[tel]'] = '12345678901';
        $form['profile[sex]'] = '1';
        $form['profile[birthday]'] = '1900-01-01';
        $form['profile[province]'] = '2';

        /*
         * *note: Symfony functional tests exercise your code by directly calling the Symfony kernel. They're not run through a web browser and therefore don't support javascript (which is simply not executed).
         */
        //$form['city'] = '8';  //城市js动态加载的
        $form['profile[income]'] = '102';
        $form['profile[profession]'] = '1';
        $form['profile[industry_code]'] = '1';
        $form['profile[work_section_code]'] = '1';
        $form['profile[education]'] = '1';

        //不理解，如果不去掉，值会变成 1,2,1,2(原本数据是：1,2)
        $form['profile[hobby]'][0]->untick();
        $form['profile[hobby]'][1]->untick();
        //选择
        $form['profile[hobby]'][5]->tick();

        $form['profile[personalDes]'] = 'personalDes';
        $form['profile[favMusic]'] = 'favMusic';
        $form['profile[monthlyWish]'] = 'monthlyWish';
        $crawler = $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->em->clear();
        $user = $this->em->getRepository('JiliApiBundle:User')->find(1);

        $value = $form->getValues();

        //[[5.1] Parse multi-dimensional form fields to multi-dimensional array in Testing/CrawlerTrait submitForm() method. by martiros · Pull Request #9058 · laravel/framework](https://github.com/laravel/framework/pull/9058/files)
        //有多选的话，不能直接用$form->getValues()取值
        parse_str(http_build_query($form->getValues()), $parameters);
        $parameters = $parameters['profile'];

        $this->assertEquals($parameters['nick'], $user->getNick());
        $this->assertEquals($parameters['tel'], $user->getTel());
        $this->assertEquals($parameters['sex'], $user->getSex());
        $this->assertEquals($parameters['birthday'], $user->getBirthday());
        $this->assertEquals(intval($parameters['province']), $user->getProvince());
        $this->assertEquals(intval($parameters['income']), $user->getIncome());
        $this->assertEquals(intval($parameters['profession']), $user->getProfession());
        $this->assertEquals(intval($parameters['industry_code']), $user->getIndustryCode());
        $this->assertEquals(intval($parameters['work_section_code']), $user->getWorkSectionCode());
        $this->assertEquals(intval($parameters['education']), $user->getEducation());
        $this->assertEquals(implode(',', $parameters['hobby']), $user->getHobby());
        $this->assertEquals($parameters['personalDes'], $user->getPersonalDes());
        $this->assertEquals($parameters['favMusic'], $user->getFavMusic());
        $this->assertEquals($parameters['monthlyWish'], $user->getMonthlyWish());
    }

    /**
     * @group dev-merge-ui-profile-edit
     */
    public function testGetDefaultValue()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $controller = new ProfileController();
        $controller->setContainer($container);

        $user = $this->em->getRepository('JiliApiBundle:User')->find(1);
        $return = $controller->getDefaultValue($user);

        $this->assertNotNull($return['user']);
        $this->assertEquals(1, $return['province'][0]->getId());
        $this->assertEquals('直辖市', $return['province'][0]->getProvinceName());

        $this->assertEquals(100, $return['income'][4]->getId());
        $this->assertEquals('1000元以下', $return['income'][4]->getIncome());

        $this->assertEquals(1, $return['hobbyList'][0]->getId());
        $this->assertEquals('上网', $return['hobbyList'][0]->getHobbyName());

        $this->assertEquals(1, $return['userProHobby'][0]);
        $this->assertEquals(2, $return['userProHobby'][1]);

        $this->assertEquals('公务员', $return['profession'][1]);
        $this->assertEquals('农业/水产', $return['industry_code'][1]);
        $this->assertEquals('总务/人事/管理', $return['work_section_code'][1]);
        $this->assertEquals('高中以下', $return['education'][1]);
    }

    /**
     * only test method, other tests are in testEditProfile
     * @group dev-merge-ui-profile-edit
     */
    public function testEditCommitAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_profile_edit_commit');
        $crawler = $client->request('GET', $url);
        $this->assertEquals(405, $client->getResponse()->getStatusCode());
    }
}
