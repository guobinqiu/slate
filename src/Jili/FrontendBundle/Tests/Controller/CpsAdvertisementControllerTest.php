<?php

namespace Jili\FrontendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;


class CpsAdvertisementControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     **/
    private $em;

    /**
     * @var boolean 
     **/
    private $has_fixture;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $container = static::$kernel->getContainer();
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        $this->client = static::createClient();
        $this->container = $container;
        $this->em  = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        if ($this->has_fixture) {
            $this->em->close();
        }
    }

    /**
     * @group issue_680
     */
    public function testListAction()
    {
        $client = $this->client;
        $container = $this->container;
        $em = $this->em;

        $url =$container->get('router')->generate('jili_frontend_cpsadvertisement_list');
        $crawler = $this->assertEquals('/shop/list', $url);
        $client->request('GET', $url);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    /**
     * @group issue_680
     */
    public function testListSearchAction()
    {
        $client = $this->client;
        $container = $this->container;
        $em = $this->em;

        $url = $container->get('router')->generate('jili_frontend_cpsadvertisement_listsearch'  ) ;
        $this->assertEquals('/shop/list/search', $url);

        $crawler = $client->request('GET', $url ) ;
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'normal GET');

        
        $form = $crawler->filter('form[name=shoplist_search]')->form();
        $form['website_filter[keyword]']->setValue('东');
        $crawler = $client->submit($form );

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('/shop/list?q='. urlencode('东').'&wcat=-1', $client->getRequest()->getRequestUri(),'redirected uri');
    }

#        $this->assertEquals(1,1);

}
