<?php
namespace Jili\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\LoadApiDupEmailCodeData;

class ApiControllerTest extends WebTestCase
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
        $em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $container  = static::$kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadApiDupEmailCodeData();
        $fixture->setContainer($container);

        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->execute($loader->getFixtures());
        $this->container = $container;
        $this->em  = $em;
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
     * todo: testing required update the schema & method to http post
     * @group api 
     */
    public function testIsEmailDuplicated()
    {

        $client = static::createClient();

        $data = array(
            array('chiangtor@gmail.com', '1'),
            array('zchua9999@sina.cn', '0'),
        );
        $user = LoadApiDupEmailCodeData::$ROWS[0];

        $container = $this->container;
        $url = $container->get('router')->generate('_api_check_email');

        foreach($data as $r) {
            $email = $r[0];
            $expected = $r[1];
            $crawler = $client->request('POST', $url, array('email'=>$email));
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertEquals($expected, $client->getResponse()->getContent(),'expected ' . $expected . ' with email '. $email );
        }
    }
}
