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
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_profile_edit');

        //没有登录
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $session = $container->get('session');

        //login, 用户不存在
        $session->set('uid', 1000);
        $session->save();

        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //login, 用户 存在
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
        $form['hobby'] = array (
            1,
            2
        );
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

        echo "<br>line_" . __LINE__ . "_aaaaaaaaaa<pre>";
        print_r($value);

        $this->assertEquals($value['nick'], $user->getNick());
        $this->assertEquals($value['tel'], $user->getTel());
        $this->assertEquals($value['sex'], $user->getSex());
        $this->assertEquals($value['birthday'], $user->getBirthday());
        $this->assertEquals($value['province'], $user->getProvince());
        $this->assertEquals($value['income'], $user->getIncome());
        $this->assertEquals($value['profession'], $user->getProfession());
        $this->assertEquals($value['industry_code'], $user->getIndustryCode());
        $this->assertEquals($value['work_section_code'], $user->getWorkSectionCode());
        $this->assertEquals($value['education'], $user->getEducation());
        $this->assertEquals(implode(',', $value['hobby']), $user->getHobby());
        $this->assertEquals($value['personalDes'], $user->getPersonalDes());
        $this->assertEquals($value['favMusic'], $user->getFavMusic());
        $this->assertEquals($value['monthlyWish'], $user->getMonthlyWish());
    }
}
