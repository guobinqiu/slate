<?php
namespace Jili\ApiBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;

use Jili\ApiBundle\DataFixtures\ORM\Entity\LoadAdvertisermentCodeData;


class AdvertisermentTest extends KernelTestCase
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
        static :: $kernel = static :: createKernel();
        static :: $kernel->boot();

        $em = static :: $kernel->getContainer()->get('doctrine')->getManager();
        $container = static :: $kernel->getContainer();

            // purge tables;
            $purger = new ORMPurger($em);
            $executor = new ORMExecutor($em, $purger);
            $executor->purge();

                $fixture = new LoadAdvertisermentCodeData(); 
            $loader = new Loader();
            $loader->addFixture($fixture);
            $executor->execute($loader->getFixtures());

        $this->console = $container;
        $this->em = $em;
    }

    public function tearDown()
    {
        parent :: tearDown();
        $this->em->close();
    }

    /**
     * @group issue_469
     */
    public function testGetImageurlParsed()
    {
        $em = $this->em;
        $rows = LoadAdvertisermentCodeData::$ROWS;
        $uid = 105;

        $ad = $em->getRepository('JiliApiBundle:Advertiserment')->find( $rows[0]->getId());
        $expected = 'http://count.chanet.com.cn/click.cgi?a=480534&d=9340&u=105&e='.$rows[0]->getId();
        $this->assertEquals($expected,$ad->getImageurlParsed($uid), 'normal url: '. $expected);

        $ad = $em->getRepository('JiliApiBundle:Advertiserment')->find( $rows[1]->getId());
        $expected = $rows[1]->getImageurl();
        $this->assertEquals($expected,$ad->getImageurlParsed($uid), 'imageurl has no u=');

        $ad = $em->getRepository('JiliApiBundle:Advertiserment')->find( $rows[2]->getId());
        $expected =''; // $rows[1]->getImageurl();
        $this->assertEquals($expected,$ad->getImageurlParsed($uid), 'empty imageurl');

    }

}
