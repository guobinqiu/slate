<?php
namespace Jili\ApiBundle\DataFixtures\ORM\Advertiserment;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCheckinAdverListData extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface, OrderedFixtureInterface
{

    public static $ROWS;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct()
    {
        self::$ROWS = array();
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
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        //load data for testing .
        $root_dir = $this->container->get('kernel')->getRootDir();
        $fixture_dir = $root_dir.DIRECTORY_SEPARATOR.'fixtures'.DIRECTORY_SEPARATOR. 'advertiserment';
        $sql = file_get_contents($fixture_dir.DIRECTORY_SEPARATOR.'checkin_adver_list.sql');
        $r =   $manager->getConnection()->query($sql);
        $r->closeCursor();

        self::$ROWS = $manager->getRepository('JiliApiBundle:CheckinAdverList')->findAll();
        foreach(self::$ROWS as $reference => $row) {
            $this->addReference('checkinAdverList_'. $reference, $row);
        }
    }
}
