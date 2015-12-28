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
     * @group dev-merge-ui-profile-edit
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
        $form['nick'] = 'nick';
        $form['tel'] = '12345678901';
        $form['sex'] = '1';
        $form['birthday'] = '1900-01-01';
        $form['province'] = '2';

        /*
         * *note: Symfony functional tests exercise your code by directly calling the Symfony kernel. They're not run through a web browser and therefore don't support javascript (which is simply not executed).
         */
        //$form['city'] = '8';  //城市js动态加载的
        $form['income'] = '102';
        $form['profession'] = '1';
        $form['industry_code'] = '1';
        $form['work_section_code'] = '1';
        $form['education'] = '1';

        //不理解，如果不去掉，值会变成 1,2,1,2(原本数据是：1,2)
        $form['hobby'][0]->untick();
        $form['hobby'][1]->untick();
        //选择
        $form['hobby'][5]->tick();

        $form['personalDes'] = 'personalDes';
        $form['favMusic'] = 'favMusic';
        $form['monthlyWish'] = 'monthlyWish';
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

        $this->assertEquals($parameters['nick'], $user->getNick());
        $this->assertEquals($parameters['tel'], $user->getTel());
        $this->assertEquals($parameters['sex'], $user->getSex());
        $this->assertEquals($parameters['birthday'], $user->getBirthday());
        $this->assertEquals($parameters['province'], $user->getProvince());
        $this->assertEquals($parameters['income'], $user->getIncome());
        $this->assertEquals($parameters['profession'], $user->getProfession());
        $this->assertEquals($parameters['industry_code'], $user->getIndustryCode());
        $this->assertEquals($parameters['work_section_code'], $user->getWorkSectionCode());
        $this->assertEquals($parameters['education'], $user->getEducation());
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
}
