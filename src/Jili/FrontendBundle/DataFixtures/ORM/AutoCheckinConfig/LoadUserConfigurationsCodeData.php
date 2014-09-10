<?php
namespace Jili\FrontendBundle\DataFixtures\ORM\AutoCheckinConfig;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Jili\ApiBundle\Entity\UserConfigurations;

class LoadUserConfigurationsCodeData extends AbstractFixture implements ContainerAwareInterface,  FixtureInterface, OrderedFixtureInterface
{
    static public $ROWS ;

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
    public function load(ObjectManager $manager)
    {
        $user0 =  $this->getReference('user0');

        $a = new UserConfigurations();
        $a->setUser($user0);
        $a->setFlagName('auto_checkin');
        $a->setFlagData(1);

        $manager->persist($a);
        $manager->flush();
        self::$ROWS[] = $a;

        $user1 =  $this->getReference('user1');
        unset($a);

        $b = new UserConfigurations();
        $b->setUser($user1);
        $b->setFlagName('auto_checkin');
        $b->setFlagData(0);

        $manager->persist($b);
        $manager->flush();

        self::$ROWS[] = $b;
        unset($b);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
