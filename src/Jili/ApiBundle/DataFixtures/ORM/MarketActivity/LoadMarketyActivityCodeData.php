<?php
namespace Jili\ApiBundle\DataFixtures\ORM\MarketActivity;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\Advertiserment;
use Jili\ApiBundle\Entity\MarketActivity;

/**
 * 
 **/
class LoadMarketyActivityCodeData extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface, OrderedFixtureInterface
{
   public static  $ROWS;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$ROWS= array();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $ad = $this->getReference('advertiserment0');

        $row = new MarketActivity();
        $row->setAid($ad->getId() );
        $row->setBusinessName('疯狂满减，根本停不下来');
        $row->setCategoryId(5);
        $row->setActivityUrl('#');
        $row->setActivityImage('images/activity/1408084816_1920.jpeg');
        $row->setActivityDescription('Test Activity Description');
        $row->setStartTime(new \DateTime('2014-07-31 14:40:05'));
        $row->setEndTime(new \DateTime('2014-09-03 14:40:07'));
        $row->setCreateTime(new \DateTime('2014-08-15 14:40:16'));
        $row->setDeleteFlag(0);
        $manager->persist($row);
        $manager->flush();
#        $this->addReference('marketActivity0', $marketActivity);
        self::$ROWS[] = $row;
    }
}

