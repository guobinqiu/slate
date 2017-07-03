<?php
namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\FrontendBundle\Controller\SurveyController;

class SurveyControllerTest extends WebTestCase
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

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $fixture = new SurveyControllerTestFixture();
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        if ($this->em != null) {
            $this->em->close();
        }
    }

    public function testIndexPageWithoutLogin()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_survey_index');
        $client->request('GET', $url);
        $this->assertRegExp('/user\/login$/', $client->getResponse()->getTargetUrl());
    }

    public function testTopPageWithoutLogin()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_survey_top');
        $client->request('GET', $url);
        $this->assertRegExp('/user\/login$/', $client->getResponse()->getTargetUrl());
    }

    /**
     * @group dev-merge-ui-survey-top
     */
    public function testTopPageWithLogin()
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $this->login($client);

        $url = $container->get('router')->generate('_survey_top');
        $crawler = $client->request('GET', $url);
        // redirect to _homepage
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
//        $this->assertCount(1, $crawler->filter('#sop_api_url'));
//        $this->assertCount(1, $crawler->filter('#sop_point'));
//        $this->assertCount(1, $crawler->filter('#sop_app_id'));
//        $this->assertCount(1, $crawler->filter('#sop_app_mid'));
//        $this->assertCount(1, $crawler->filter('#sop_sig'));
//        $this->assertCount(1, $crawler->filter('#sop_time'));
    }

    private function login($client)
    {
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_user_login');
        $csrfToken = $container->get('form.csrf_provider')->generateCsrfToken('login');
        $client->request('POST', $url, array(
            'login' => array(
                'email' => 'user@voyagegroup.com.cn',
                'password' => '11111q',
                '_token' => $csrfToken
            )
        ), array(), array('HTTPS' => true));
        //echo $client->getResponse()->getContent();
    }

}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Services\HttpClient;

class SurveyControllerTestFixture implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user = new \Wenwen\FrontendBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();
    }
}