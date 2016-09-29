<?php
namespace Wenwen\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Wenwen\AppBundle\Entity\SsiRespondent;
use Wenwen\FrontendBundle\Controller\SsiPartnerController;

class SsiPartnerControllerTest extends WebTestCase
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

        $fixture = new SsiPartnerControllerTestFixture();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());

        $this->container = $container;
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

    /**
     * @group dev-merge-ui-survey-list-ssi-agreement
     */
    public function testPermissionYes()
    {
        $client = static::createClient();
        $container = $this->container;
        $em = $this->em;
        $user = SsiPartnerControllerTestFixture::$USER;

        $url = $container->get('router')->generate('_ssi_partner_permission');

        $crawler = $client->request('GET', $url);

        //not login, will follow redirect to _ssi_partner_error
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('/ssi_partner/error'));

        $this->login($client);

        //status: login, will show permission page
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $form = $crawler->filter('form[id=ssi_permission_form]')->form();
        $crawler = $client->submit($form);
        //$this->assertEquals('http://localhost/ssi_partner/commit', $client->getRequest()->getUri());
        $this->assertRegExp('/ssi_partner\/commit$/', $client->getRequest()->getUri());
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        //redirect redirectAction
        $crawler = $client->followRedirect();
        //$this->assertEquals('http://localhost/ssi_partner/redirect', $client->getRequest()->getUri());
        $this->assertRegExp('/ssi_partner\/redirect$/', $client->getRequest()->getUri());
        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $ssi_respondent = $em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user->getId());

        //redirect survey site
        $crawler = $client->followRedirect();
        $this->assertEquals('http://tracking.surveycheck.com/aff_c?aff_id=1346&aff_sub5=wwcn-' . $ssi_respondent->getId() . '&offer_id=3135', $client->getRequest()->getUri());

        //check db
        $em->clear();
        $this->assertNotEmpty($ssi_respondent);
        $this->assertEquals(1, $ssi_respondent->getStatusFlag());
    }


    public function testPrescreenActionWithValidStatusFlag()
    {
        $client = static::createClient();
        $container = $this->container;
        $user = SsiPartnerControllerTestFixture::$USER;
        $this->login($client);

        $crawler = $client->request('GET', $container->get('router')->generate('_ssi_partner_permission'));

        $form = $crawler->filter('form[id=ssi_permission_form]')->form();
        $crawler = $client->submit($form);

        $ssi_respondent = $this->em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user->getId());

        $crawler = $client->request('GET', $container->get('router')->generate('_ssi_partner_prescreen'));
        $this->assertRegExp('/ssi_partner\/prescreen$/', $client->getRequest()->getUri());

        //submit redirect survey site
        $form = $crawler->filter('form[id=ssi_redirect_form]')->form();
        $crawler = $client->submit($form);
        $this->assertRegExp('/ssi_partner\/redirect$/', $client->getRequest()->getUri());

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        //redirect survey site
        $crawler = $client->followRedirect();
        $this->assertEquals('http://tracking.surveycheck.com/aff_c?aff_id=1346&aff_sub5=wwcn-' . $ssi_respondent->getId() . '&offer_id=3135', $client->getRequest()->getUri());
    }

    /**
     * @group dev-merge-ui-survey-list-ssi-agreement
     */
    public function testPrescreeningCompleteAction()
    {
        $client = static::createClient();
        $container = $this->container;
        $user = SsiPartnerControllerTestFixture::$USER;
        $this->login($client);

        $crawler = $client->request('GET', $container->get('router')->generate('_ssi_partner_permission'));

        $form = $crawler->filter('form[id=ssi_permission_form]')->form();
        $crawler = $client->submit($form);

        $crawler = $client->request('GET', $container->get('router')->generate('_ssi_partner_prescreeningcomplete'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check db
        $ssi_respondent = $this->em->getRepository('WenwenAppBundle:SsiRespondent')->findOneByUserId($user->getId());
        $this->assertNotEmpty($ssi_respondent);
        $this->assertEquals(10, $ssi_respondent->getStatusFlag());

        $user = $this->em->getRepository('WenwenFrontendBundle:User')->find($user->getId());
        $this->assertEquals(SsiPartnerControllerTestFixture::$USER->getPoints() + 1, $user->getPoints());
    }

    /**
     * @group dev-merge-ui-survey-list-ssi-agreement
     */
    public function testErrorActionAction()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $em = $this->em;
        $url = $container->get('router')->generate('_ssi_partner_error');
        $crawler = $client->request('GET', $url);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
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
        ), array(), array('HTTPS' => true));
    }
}

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SsiPartnerControllerTestFixture implements FixtureInterface, ContainerAwareInterface
{
    public static $USER;
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new \Wenwen\FrontendBundle\Entity\User();
        $user->setNick(__CLASS__);
        $user->setEmail('test@d8aspring.com');
        $user->setIsEmailConfirmed(1);
        $user->setPwd('password');
        $manager->persist($user);
        $manager->flush();

        self::$USER = $user;
    }
}
