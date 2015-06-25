<?php
namespace Jili\FrontendBundle\Tests\Entity;

use Jili\Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;


use Jili\FrontendBundle\DataFixtures\ORM\Entity\LoadAnChanetAdvertisement;
use Jili\FrontendBundle\Entity\ChanetAdvertisement;

class ChanetAdvertisementTest extends KernelTestCase
{
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp() {
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();
        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container  = static :: $kernel->getContainer();

        // purge tables;
        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);

        $fixture = new LoadAnChanetAdvertisement();
        $loader = new Loader();
        $loader->addFixture($fixture);

        $executor->purge();
        $executor->execute($loader->getFixtures());

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->close();
    }
    /**
     * @group issue_717
     */
    public function test_getRedirectUrlWithUserId()
    {
        # $model  = new ChanetAdvertisement();
        $entity = LoadAnChanetAdvertisement::$ENTITIES[0];

        $sample = 'http://count.chanet.com.cn/click.cgi?a=480534&d=383449&u=19&e=19_'.$entity->getId().'&url=http%3A%2F%2Fwww.supuy.com%2F';

        $this->assertEquals($sample, $entity->getRedirectUrlWithUserId(19), 'redirect url');
    }
}

