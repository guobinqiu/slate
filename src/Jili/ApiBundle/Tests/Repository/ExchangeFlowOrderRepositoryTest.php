<?php
namespace Jili\ApiBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Jili\ApiBundle\DataFixtures\ORM\LoadUserData;

class ExchangeFlowOrderRepositoryTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $container;
    private $user;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

        // purge tables
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->purge();

        // load fixtures
        $fixture = new LoadUserData();
        $fixture->setContainer($container);
        $loader = new Loader();
        $loader->addFixture($fixture);
        $executor->execute($loader->getFixtures());
        $this->user = LoadUserData :: $USERS[0];

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
     * @group issue_682
     */
    public function testInsert() {
        $em = $this->em;
        $user_id = $this->user->getId();
        $params = array (
            'user_id' => $user_id,
            'provider' => '移动',
            'province' => '上海',
            'custom_product_id' => 'custom_product_id',
            'packagesize' => '30',
            'custom_prise' => '4.000'
        );
        $exchangeFlowOrder = $em->getRepository('JiliApiBundle:ExchangeFlowOrder')->insert($params);
        $this->assertNotNull($exchangeFlowOrder);
    }
}