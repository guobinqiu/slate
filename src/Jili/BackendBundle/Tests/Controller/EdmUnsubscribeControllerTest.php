<?php
namespace Jili\BackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Jili\BackendBundle\Controller\EdmUnsubscribeController;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadUserEdmUnsubscribeData;

class EdmUnsubscribeControllerTest extends WebTestCase {

    private $em;
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserEdmUnsubscribeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());

        $this->em = $em;
        $this->container = $container;
    }
    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_450
     * @group CheckForAdd
     */
    public function testCheckForAdd() {
        $container = $this->container;
        $controller = new EdmUnsubscribeController();
        $controller->setContainer($container);

        $email = null;
        $return = $controller->checkForAdd($email);
        $this->assertEquals($container->getParameter('reg_en_mail'), $return['message']);

        $email = 'zhangmm11@voyagegroup.com.cn';
        $return = $controller->checkForAdd($email);
        $this->assertEquals($container->getParameter('chnage_no_email'), $return['message']);

        $email = 'zhangmm@voyagegroup.com.cn';
        $return = $controller->checkForAdd($email);
        $this->assertEquals($container->getParameter('user_edm_unsubscribe_is_exist'), $return['message']);

        $email = 'zhangmm2@voyagegroup.com.cn';
        $em = $this->em;
        $return = $controller->checkForAdd($email);
        $user = $em->getRepository('JiliApiBundle:User')->findByEmail($email);
        $this->assertEquals('', $return['message']);
        $this->assertEquals($user[0]->getId(), $return['user_id']);
    }

    /**
     * @group issue_450
     * @group ListAction
     */
    public function testListAction() {
        $client = static :: createClient();
        $container = $this->container;

        $url = $container->get('router')->generate('_edm_unsubscribe_list', array (), true);
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("zhangmm@voyagegroup.com.cn")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("zhangmm1@voyagegroup.com.cn")')->count() > 0);

        $crawler = $client->request('GET', $url, array (
            'email' => 'zhangmm@voyagegroup.com.cn'
        ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("zhangmm@voyagegroup.com.cn")')->count() > 0);
        $this->assertTrue($crawler->filter('html:contains("zhangmm1@voyagegroup.com.cn")')->count() == 0);
    }

    /**
     * @group issue_450
     * @group AddIndexAction
     */
    public function testAddIndexAction() {
        $client = static :: createClient();
        $container = $this->container;

        $url = $container->get('router')->generate('_edm_unsubscribe_add_index', array (), true);
        echo $url, PHP_EOL;
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @group issue_450
     * @group AddConfirmAction
     */
    public function testAddConfirmAction() {
        $client = static :: createClient();
        $container = $this->container;
        $em = $this->em;

        $email = 'zhangmm2@voyagegroup.com.cn';
        $url = $container->get('router')->generate('_edm_unsubscribe_add_confirm', array (), true);
        echo $url, PHP_EOL;
        $crawler = $client->request('POST', $url, array (
            'email' => $email
        ));
        $edms = $em->getRepository('JiliApiBundle:UserEdmUnsubscribe')->findByEmail($email);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, count($edms));
    }

}