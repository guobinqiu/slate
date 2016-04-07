<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SsiProjectSurveyControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;

    /**
     * {@inheritdoc}
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

        $fixture = new SsiProjectSurveyControllerTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

    public function testCoverPageWithoutLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/ssi_project_survey/information/1');

        $this->assertRegExp('/user\/login$/', $client->getResponse()->getTargetUrl());
    }

    public function testCoverPageWithLogin()
    {
        $client = static::createClient();

        $container = $client->getContainer();
        $url = $container->get('router')->generate('_login', array(), true);
        $client->request('POST', $url, array('email' => 'test@d8aspring.com', 'pwd' => '1qaz2wsx', 'remember_me' => '1'));
        $client->followRedirect();

        $client->request('GET', '/ssi_project_survey/information/1');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SsiProjectSurveyControllerTestFixture implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setIsEmailConfirmed(1);
        $user->setPasswordChoice(\Jili\ApiBundle\Entity\User::PWD_WENWEN);
        $manager->persist($user);
        $manager->flush();

        $user_wenwen_login = new \Jili\ApiBundle\Entity\UserWenwenLogin();
        $user_wenwen_login->setUser($user);
        $user_wenwen_login->setLoginPasswordSalt('★★★★★アジア事業戦略室★★★★★');
        $user_wenwen_login->setLoginPasswordCryptType('blowfish');
        $user_wenwen_login->setLoginPassword('9rNV0b+0hnA=');
        $manager->persist($user_wenwen_login);
        $manager->flush();
    }
}
