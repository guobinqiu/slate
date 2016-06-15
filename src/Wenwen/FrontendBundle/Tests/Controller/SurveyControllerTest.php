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

    public function testGetSOPSurveyList()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $surveyListService = $container->get('app.survey_list_service');
        $sop_api_url = 'https://partners.surveyon.com.dev.researchpanelasia.com/api/v1_1/surveys/js?app_id=27&app_mid=1&sig=f0620765bb314ff9903f95aae5b7179030503886d82d1a2d2b717ecb5f5c4d52&time=1465975276&sop_callback=surveylistCallback';
        echo $surveyListService->getSOPSurveyList($sop_api_url);
    }

    public function testIndexPageWithoutLogin()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_survey_index');
        $client->request('GET', $url);
        $this->assertRegExp('/user\/login$/', $client->getResponse()->getTargetUrl());
    }

    /**
     * @group dev-merge-ui-survey-list
     * @group dev-merge-ui-survey-top
     */
    public function testIndexPageWithLogin()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('_login', array (), true);
$this->showTimeToMicroseconds('HOHO10');
        $client->request('POST', $url, array (
            'email' => 'user@voyagegroup.com.cn',
            'pwd' => '11111q',
            'remember_me' => '1'
        ));
        //$client->followRedirect();
$this->showTimeToMicroseconds('HOHO11');
        $url = $container->get('router')->generate('_survey_index');
$this->showTimeToMicroseconds('HOHO12');
        $crawler = $client->request('GET', $url);
$this->showTimeToMicroseconds('HOHO13');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("问卷列表")')->count() > 0);

//        $this->assertCount(1, $crawler->filter('#sop_api_url'));
//        $this->assertCount(1, $crawler->filter('#sop_point'));
//        $this->assertCount(1, $crawler->filter('#sop_app_id'));
//        $this->assertCount(1, $crawler->filter('#sop_app_mid'));
//        $this->assertCount(1, $crawler->filter('#sop_sig'));
//        $this->assertCount(1, $crawler->filter('#sop_time'));
    }

    public function testTopPageWithoutLogin()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $url = $container->get('router')->generate('_survey_top');
        $client->request('GET', $url);
        $this->assertRegExp('/user\/login$/', $client->getResponse()->getTargetUrl());
    }

public function showTimeToMicroseconds($count = 0) {
    $t = microtime(true);
    $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
    $d = new \DateTime(date('Y-m-d H:i:s.' . $micro, $t));

    echo "\n" . $this->getname() . ' [' . $count .  '] ' .  $d->format("Y-m-d H:i:s.u"); 
}

    /**
     * @group dev-merge-ui-survey-top
     */
    public function testTopPageWithLogin()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $url = $container->get('router')->generate('_login', array (), true);
$this->showTimeToMicroseconds('HOHO10');
        $crawler = $client->request('POST', $url, array (
            'email' => 'user@voyagegroup.com.cn',
            'pwd' => '11111q',
            'remember_me' => '1'
        ));
        //$client->followRedirect();
$this->showTimeToMicroseconds('HOHO11');
        $url = $container->get('router')->generate('_survey_top');
$this->showTimeToMicroseconds('HOHO12');
        $crawler = $client->request('GET', $url);
$this->showTimeToMicroseconds('HOHO13');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $this->assertCount(1, $crawler->filter('#sop_api_url'));
//        $this->assertCount(1, $crawler->filter('#sop_point'));
//        $this->assertCount(1, $crawler->filter('#sop_app_id'));
//        $this->assertCount(1, $crawler->filter('#sop_app_mid'));
//        $this->assertCount(1, $crawler->filter('#sop_sig'));
//        $this->assertCount(1, $crawler->filter('#sop_time'));
    }

    /**
     * @group dev-merge-ui-survey-top
     */
    public function testGetSopParams()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;

        $controller = new SurveyController();
        $controller->setContainer($container);

        $sop_respondent = $em->getRepository('JiliApiBundle:SopRespondent')->retrieveOrInsertByUserId(1);
        $sop_config = $container->getParameter('sop');
        $return = $controller->getSopParams($sop_config, $sop_respondent->getId());
        $this->assertNotEmpty($return['sop_params']['app_id']);
        $this->assertNotEmpty($return['sop_params']['app_mid']);
        $this->assertNotEmpty($return['sop_params']['time']);
        $this->assertNotEmpty($return['sop_api_url']);
        $this->assertNotEmpty($return['sop_point']);
        $this->assertNotEmpty($return['sop_params']['sig']);
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wenwen\FrontendBundle\Services\HttpClient;

class SurveyControllerTestFixture implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user = new \Jili\ApiBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('user@voyagegroup.com.cn');
        $user->setPoints(100);
        $user->setIsInfoSet(0);
        $user->setIconPath('test/test_icon.jpg');
        $user->setRewardMultiple(1);
        $user->setPwd('11111q');
        $user->setIsEmailConfirmed(1);
        $user->setRegisterDate(new \DateTime());
        $manager->persist($user);
        $manager->flush();
    }
}