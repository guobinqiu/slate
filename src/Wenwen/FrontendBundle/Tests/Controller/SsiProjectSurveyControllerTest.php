<?php

namespace Wenwen\FrontendBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SsiProjectSurveyControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $container = static::$kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->purge();

        $fixture = new SsiProjectSurveyControllerTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());
    }
    public function tearDown()
    {
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

        $this->login($client);

        $ssi_project = SsiProjectSurveyControllerTestFixture::$SSI_PROJECT;
        $client->request('GET', '/ssi_project_survey/information/'.$ssi_project->getId());
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $this->assertRegExp(sprintf('/s%d/', $ssi_project->getId()), $client->getResponse()->getContent());
    }

    public function testCoverPageForUnavailableProject()
    {
        $client = static::createClient();

        $this->login($client);

        $client->request('GET', '/ssi_project_survey/information/999');
        $this->assertSame(404, $client->getResponse()->getStatusCode(), 'Project is not available');
    }

    public function testCompletePage()
    {
        $client = static::createClient();

        $this->login($client);

        $em = static::$kernel->getContainer()->get('doctrine')->getManager();
        $ssi_surveys = \VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery::retrieveSurveysForRespondent(
            $em->getConnection(),
            SsiProjectSurveyControllerTestFixture::$SSI_RESPONDENT->getId()
        );
        $this->assertCount(1, $ssi_surveys, '1 SSI survey is available');

        $client->request('GET', '/ssi_project_survey/complete');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $ssi_surveys = \VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery::retrieveSurveysForRespondent(
            $em->getConnection(),
            SsiProjectSurveyControllerTestFixture::$SSI_RESPONDENT->getId()
        );
        $this->assertCount(0, $ssi_surveys, 'SSI survey is closed');

        $em->close();
    }

    private function login($client)
    {
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_user_login');
        $csrfToken = $container->get('form.csrf_provider')->generateCsrfToken('login');
        $client->request('POST', $url, array(
            'login' => array(
                'email' => 'test@d8aspring.com',
                'password' => 'password',
                '_token' => $csrfToken
            )
        ));
        //echo $client->getResponse()->getContent();
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SsiProjectSurveyControllerTestFixture implements FixtureInterface, ContainerAwareInterface
{
    public static $USER, $SSI_PROJECT, $SSI_RESPONDENT;
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
        $user->setPwd('password');
        $manager->persist($user);
        $manager->flush();

        $ssi_project = new \Wenwen\AppBundle\Entity\SsiProject();
        $ssi_project->setStatusFlag(1);
        $manager->persist($ssi_project);
        $manager->flush();

        $ssi_respondent = new \Wenwen\AppBundle\Entity\SsiRespondent();
        $ssi_respondent->setUser($user);
        $ssi_respondent->setStatusFlag(\Wenwen\AppBundle\Entity\SsiRespondent::STATUS_ACTIVE);
        $manager->persist($ssi_respondent);
        $manager->flush();

        $ssi_project_respondent = new \Wenwen\AppBundle\Entity\SsiProjectRespondent();
        $ssi_project_respondent->setSsiRespondent($ssi_respondent);
        $ssi_project_respondent->setSsiProject($ssi_project);
        $ssi_project_respondent->setSsiMailBatchId(1);
        $ssi_project_respondent->setStartUrlId('hoge');
        $ssi_project_respondent->setAnswerStatus(1);
        $ssi_project_respondent->setStashData(array(
            'startUrlHead' => 'http://www.d8aspring.com/?dummy=ssi-survey&id=',
          ));
        $manager->persist($ssi_project_respondent);
        $manager->flush();

        self::$USER = $user;
        self::$SSI_RESPONDENT = $ssi_respondent;
        self::$SSI_PROJECT = $ssi_project;
    }
}
